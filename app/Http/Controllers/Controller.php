<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\NewWorksheet;
use App\PhilIndWorksheet;
use App\Receipt;
use App\ReceiptArchive;
use App\Warehouse;
use DB;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    
    protected function trackingValidate($tracking)
    {
        $pattern = '/^[a-z0-9]+$/i';
        if (preg_match($pattern, $tracking)) {
            return true;
        } else {
            return false;
        }
    }


    protected function getTableColumns($table)
    {       
        return Schema::getColumnListing($table);
    }
        

    protected function checkReceipt($id, $receipt_id, $which_admin, $tracking_main, $receipt_number = null)
    {
        $message = '';
        $receipt = Receipt::where('tracking_main',$tracking_main)->first();
        $update_date = Date('Y-m-d', strtotime('+4 days'));

        if ($receipt_id == null) {
            if (!$receipt) {
                if ($which_admin === 'ru') {
                    $message = 'ВНИМАНИЕ! В ТАБЛИЦЕ «КВИТАНЦИИ» ОСТУСТСТВУЕТ ТРЕКИНГ НОМЕР ...('.$tracking_main.')!';
                    $archive = [
                        'worksheet_id' => $id,
                        'tracking_main' => $tracking_main,
                        'which_admin' => 'ru',
                        'update_date' => $update_date,
                        'status' => false,
                        'description' => 'ВНИМАНИЕ! В ТАБЛИЦЕ «КВИТАНЦИИ» ОСТУСТСТВУЕТ ТРЕКИНГ НОМЕР ...('.$tracking_main.')!'
                    ];
                    ReceiptArchive::create($archive);
                }
                else if ($which_admin === 'en') {
                    $message = 'WARNING! DETECTED TRACKING NUMBERS MISSING IN THE RECEIPTS SHEET ...('.$tracking_main.')!';
                    $archive = [
                        'worksheet_id' => $id,
                        'tracking_main' => $tracking_main,
                        'which_admin' => 'en',
                        'update_date' => $update_date,
                        'status' => false,
                        'description' => 'WARNING! DETECTED TRACKING NUMBERS MISSING IN THE RECEIPTS SHEET ...('.$tracking_main.')!'
                    ];
                    ReceiptArchive::create($archive);
                }
            }
            else {
                ReceiptArchive::where('tracking_main', $tracking_main)->delete();
            }
        } 
        elseif ($id == null) {
            if ($which_admin === 'en') {
                $message = 'WARNING! DETECTED TRACKING NUMBERS MISSING IN THE WORK SHEET ...('.$tracking_main.')!';
                $archive = [
                    'receipt_id' => $receipt_id,
                    'receipt_number' => $receipt_number,
                    'tracking_main' => $tracking_main,
                    'which_admin' => 'en',
                    'description' => 'WARNING! DETECTED TRACKING NUMBERS MISSING IN THE WORK SHEET ...('.$tracking_main.')!'
                ];
                ReceiptArchive::create($archive);
            }
            if ($which_admin === 'ru') {
                $message = 'ВНИМАНИЕ! В РАБОЧЕМ ЛИСТЕ ОСТУСТСТВУЕТ ТРЕКИНГ НОМЕР ...('.$tracking_main.')!';
                $archive = [
                    'receipt_id' => $receipt_id,
                    'receipt_number' => $receipt_number,
                    'tracking_main' => $tracking_main,
                    'which_admin' => 'ru',
                    'description' => 'ВНИМАНИЕ! В РАБОЧЕМ ЛИСТЕ ОСТУСТСТВУЕТ ТРЕКИНГ НОМЕР ...('.$tracking_main.')!'
                ];
                ReceiptArchive::create($archive);
            }
        }      
        
        return $message;
    }


    protected function updateWarehouse($old_pallet, $new_pallet, $old_tracking, $new_tracking = null)
    {
        $track_arr = [];
        $tracking_main = ($new_tracking)?$new_tracking:$old_tracking;
        
        if ($new_pallet) {
            
            $result = Warehouse::where('pallet', $new_pallet)->first();  

            if ((stripos($tracking_main, 'CD') !== false) || (stripos($tracking_main, 'BL') !== false)){
                $which_admin = 'ru';
            }
            else if ((stripos($tracking_main, 'IN') !== false) || (stripos($tracking_main, 'NE') !== false)){
                $which_admin = 'en';
            }
            else{
                return false;
            }         

            // Adding tracking to pallet
            if ($result) {
                if ($which_admin !== $result->which_admin) {
                    return false;
                }
                $track_arr = json_decode($result->tracking_numbers);
                if (!in_array($tracking_main, $track_arr)) $track_arr[] = $tracking_main;            
                $track_arr = json_encode($track_arr);
                $result->tracking_numbers = $track_arr;
                $result->save();
            }
            else{
                $track_arr[] = $tracking_main;
                $track_arr = json_encode($track_arr);
                Warehouse::create([
                    'pallet' => $new_pallet,
                    'tracking_numbers' => $track_arr,
                    'which_admin' => $which_admin
                ]);
            }

            // Removing tracking from old pallet
            if ($old_pallet) {
                $this->removeTrackingFromPallet($old_pallet, $tracking_main);                 
            }

            // Removing old tracking from pallet
            if ($old_pallet && $old_tracking && $new_tracking) {
                $this->removeTrackingFromPallet($old_pallet, $old_tracking);                  
            }
        }
        elseif ($old_pallet && !$new_pallet){           
            // Removing tracking from old pallet
            $this->removeTrackingFromPallet($old_pallet, $tracking_main);
        } 

        return true;              
    }


    private function removeTrackingFromPallet($pallet, $tracking)
    {
        $result = Warehouse::where('pallet', $pallet)->first();
        
        if ($result) {
            $track_arr = json_decode($result->tracking_numbers);                
            while (($i = array_search($tracking, $track_arr)) !== false) {
                unset($track_arr[$i]);
            }
            $temp = [];
            foreach ($track_arr as $key => $value) {
                $temp[] = $value;
            }
            $track_arr = $temp;
            if (!$track_arr) $result->delete();
            else{
                $new_arr = json_encode($track_arr);
                $result->tracking_numbers = $new_arr;
                $result->save();
            }                              
        }

        return true;
    }


    protected function updateWarehouseWorksheet($pallet, $tracking, $new_pallet = false)
    {
        $lot = '';        
       
        if ((stripos($tracking, 'CD') !== false) || (stripos($tracking, 'BL') !== false)){            
            $result = NewWorksheet::where('tracking_main', $tracking)->first();
            if ($result) $lot = $result->batch_number;             
            
            if (!$new_pallet) {
                $this->updateNotificationsRu($pallet, $lot, $tracking);
                NewWorksheet::where('tracking_main', $tracking)
                ->update([
                    'pallet_number' => null
                ]);
            }
            else{               
                NewWorksheet::where('tracking_main', $tracking)
                ->update([
                    'pallet_number' => $new_pallet
                ]);
                $this->updateWarehouseLot($tracking, $lot, 'ru', $pallet, $lot);
            }
        }
        else if ((stripos($tracking, 'IN') !== false) || (stripos($tracking, 'NE') !== false)){
            $result = PhilIndWorksheet::where('tracking_main', $tracking)->first();
            if ($result) $lot = $result->lot;             
            
            if (!$new_pallet) {
                $this->updateNotificationsEn($pallet, $lot, $tracking);
                PhilIndWorksheet::where('tracking_main', $tracking)
                ->update([
                    'pallet_number' => null
                ]);
            }
            else{               
                PhilIndWorksheet::where('tracking_main', $tracking)
                ->update([
                    'pallet_number' => $new_pallet
                ]);
                $this->updateWarehouseLot($tracking, $lot, 'en', $pallet, $lot);
            }
        }

        $this->removeTrackingFromPallet($pallet, $tracking);

        return true;
    }


    private function checkMaxLotRu($pallet, $lot)
    {
        $lots = NewWorksheet::where([
            ['pallet_number',$pallet],
            ['batch_number',$lot]
        ])->get();
        $other_lots = NewWorksheet::where([
            ['pallet_number',$pallet],
            ['batch_number','<>',null],
            ['batch_number','<>',$lot]
        ])->get();
        $empty_lots = NewWorksheet::where([
            ['pallet_number',$pallet],
            ['batch_number',null]
        ])->get();

        // Check of batch number
        $batch_number = $other_lots->pluck('batch_number')->toArray();
        if ($batch_number) {
            $batch_number = array_count_values($batch_number);            
            $batch_number = array_keys($batch_number, max($batch_number))[0];
            $max_other_lots = NewWorksheet::where([
                ['pallet_number',$pallet],
                ['batch_number',$batch_number]
            ])->get();
            if ($max_other_lots->count() > $lots->count()) {
                $lots = $max_other_lots;
                $lot = $batch_number;
                $other_lots = NewWorksheet::where([
                    ['pallet_number',$pallet],
                    ['batch_number','<>',null],
                    ['batch_number','<>',$batch_number]
                ])->get();
            }
        }

        return [$lots, $other_lots, $empty_lots, $lot];
    }


    private function checkMaxLotEn($pallet, $lot)
    {
        $lots = PhilIndWorksheet::where([
            ['pallet_number',$pallet],
            ['lot',$lot]
        ])->get();
        $other_lots = PhilIndWorksheet::where([
            ['pallet_number',$pallet],
            ['lot','<>',null],
            ['lot','<>',$lot]
        ])->get();
        $empty_lots = PhilIndWorksheet::where([
            ['pallet_number',$pallet],
            ['lot',null]
        ])->get();

        // Check of batch number
        $batch_number = $other_lots->pluck('lot')->toArray();
        if ($batch_number) {
            $batch_number = array_count_values($batch_number);            
            $batch_number = array_keys($batch_number, max($batch_number))[0];
            $max_other_lots = PhilIndWorksheet::where([
                ['pallet_number',$pallet],
                ['lot',$batch_number]
            ])->get();
            if ($max_other_lots->count() > $lots->count()) {
                $lots = $max_other_lots;
                $lot = $batch_number;
                $other_lots = PhilIndWorksheet::where([
                    ['pallet_number',$pallet],
                    ['lot','<>',null],
                    ['lot','<>',$batch_number]
                ])->get();
            }
        }

        return [$lots, $other_lots, $empty_lots, $lot];
    }


    protected function updateNotificationsRu($old_pallet, $old_lot, $tracking = false)
    {
        $notifications = (object)['pallet'=>'','tracking'=>''];
        
        $warehouse = Warehouse::where('pallet',$old_pallet)->first();
        if ($warehouse) {
            if($warehouse->notifications){
                $notifications = json_decode($warehouse->notifications);
            }
        }

        if ($old_lot) {
            // For other lots
            $other_lots = $this->checkMaxLotRu($old_pallet, $old_lot)[1];                
            if ($other_lots) $temp_arr = $other_lots->pluck('tracking_main')->toArray();

            if ($temp_arr) {
                if ($tracking) {
                    while (($i = array_search($tracking, $temp_arr)) !== false) {
                        unset($temp_arr[$i]);
                    }
                    $temp = [];
                    foreach ($temp_arr as $key => $value) {
                        $temp[] = $value;
                    }
                    $temp_arr = $temp;
                }

                if ($temp_arr) {
                    $other_track_arr = implode(",", $temp_arr);
                    $notifications_pallet = json_decode($notifications->pallet);
                    if (is_object($notifications_pallet)) {
                        $notifications_pallet->other_arr = 'CHANGE OR DELETE PALLET NO. FOR THE PARCELS ('.$other_track_arr.') AND TRY AGAIN';
                    }
                    else{
                        $notifications_pallet = (object)['other_arr'=>'','empty_arr'=>''];
                    }
                    $notifications->pallet = json_encode($notifications_pallet);                  
                }
                else{
                    $notifications_pallet = json_decode($notifications->pallet);
                    if (is_object($notifications_pallet)) {
                        $notifications_pallet->other_arr = '';
                    }
                    else{
                        $notifications_pallet = (object)['other_arr'=>'','empty_arr'=>''];
                    }
                    $notifications->pallet = json_encode($notifications_pallet);
                }
            } 
            elseif ($warehouse) {
                $notifications_pallet = json_decode($notifications->pallet);
                if (is_object($notifications_pallet)) {
                    $notifications_pallet->other_arr = '';
                }
                else{
                    $notifications_pallet = (object)['other_arr'=>'','empty_arr'=>''];
                }
                $notifications->pallet = json_encode($notifications_pallet);
            } 
        }
        else{
            // For empty lots
            $empty_lots = NewWorksheet::where([
                ['pallet_number',$old_pallet],
                ['batch_number',null]
            ])->get();                
            if ($empty_lots) $temp_arr = $empty_lots->pluck('tracking_main')->toArray();

            $first_lot = NewWorksheet::where([
                ['pallet_number',$old_pallet],
                ['batch_number', '<>',null]
            ])->first();           

            if ($temp_arr && $first_lot) {

                $result = $this->checkMaxLotRu($old_pallet, $first_lot->batch_number);
                $lots = $result[0];            
                $other_lots = $result[1];           
                $empty_lots = $result[2];
                $lot = $result[3];
                
                if ($tracking) {
                    while (($i = array_search($tracking, $temp_arr)) !== false) {
                        unset($temp_arr[$i]);
                    }
                    $temp = [];
                    foreach ($temp_arr as $key => $value) {
                        $temp[] = $value;
                    }
                    $temp_arr = $temp;
                }

                if ($temp_arr && $lots->count() > 3) {
                    $other_track_arr = implode(",", $temp_arr);
                    $notifications_pallet = json_decode($notifications->pallet);
                    if (is_object($notifications_pallet)) {
                        $notifications_pallet->empty_arr = 'CHANGE PALLET NO. FOR THE MISSING PARCELS ('.$other_track_arr.')';
                    }
                    else{
                        $notifications_pallet = (object)['other_arr'=>'','empty_arr'=>''];
                    }
                    $notifications->pallet = json_encode($notifications_pallet);                  
                }
                else{
                    $notifications_pallet = json_decode($notifications->pallet);
                    if (is_object($notifications_pallet)) {
                        $notifications_pallet->empty_arr = '';
                    }
                    else{
                        $notifications_pallet = (object)['other_arr'=>'','empty_arr'=>''];
                    }
                    $notifications->pallet = json_encode($notifications_pallet);
                }
            } 
            elseif ($warehouse) {
                $notifications_pallet = json_decode($notifications->pallet);
                if (is_object($notifications_pallet)) {
                    $notifications_pallet->empty_arr = '';
                }
                else{
                    $notifications_pallet = (object)['other_arr'=>'','empty_arr'=>''];
                }
                $notifications->pallet = json_encode($notifications_pallet);
            } 
        }             
        
        $notifications = json_encode($notifications);
        Warehouse::where('pallet',$old_pallet)->update([
            'notifications' => $notifications
        ]);

        return true;
    }


    protected function updateNotificationsEn($old_pallet, $old_lot, $tracking = false)
    {
        $notifications = (object)['pallet'=>'','tracking'=>''];
        
        $warehouse = Warehouse::where('pallet',$old_pallet)->first();
        if ($warehouse) {
            if($warehouse->notifications){
                $notifications = json_decode($warehouse->notifications);
            }
        }

        if ($old_lot) {
            // For other lots
            $other_lots = $this->checkMaxLotEn($old_pallet, $old_lot)[1];                
            if ($other_lots) $temp_arr = $other_lots->pluck('tracking_main')->toArray();

            if ($temp_arr) {
                if ($tracking) {
                    while (($i = array_search($tracking, $temp_arr)) !== false) {
                        unset($temp_arr[$i]);
                    }
                    $temp = [];
                    foreach ($temp_arr as $key => $value) {
                        $temp[] = $value;
                    }
                    $temp_arr = $temp;
                }

                if ($temp_arr) {
                    $other_track_arr = implode(",", $temp_arr);
                    $notifications_pallet = json_decode($notifications->pallet);
                    if (is_object($notifications_pallet)) {
                        $notifications_pallet->other_arr = 'CHANGE OR DELETE PALLET NO. FOR THE PARCELS ('.$other_track_arr.') AND TRY AGAIN';
                    }
                    else{
                        $notifications_pallet = (object)['other_arr'=>'','empty_arr'=>''];
                    }
                    $notifications->pallet = json_encode($notifications_pallet);                  
                }
                else{
                    $notifications_pallet = json_decode($notifications->pallet);
                    if (is_object($notifications_pallet)) {
                        $notifications_pallet->other_arr = '';
                    }
                    else{
                        $notifications_pallet = (object)['other_arr'=>'','empty_arr'=>''];
                    }
                    $notifications->pallet = json_encode($notifications_pallet);
                }
            } 
            elseif ($warehouse) {
                $notifications_pallet = json_decode($notifications->pallet);
                if (is_object($notifications_pallet)) {
                    $notifications_pallet->other_arr = '';
                }
                else{
                    $notifications_pallet = (object)['other_arr'=>'','empty_arr'=>''];
                }
                $notifications->pallet = json_encode($notifications_pallet);
            } 
        }
        else{
            // For empty lots
            $empty_lots = PhilIndWorksheet::where([
                ['pallet_number',$old_pallet],
                ['lot',null]
            ])->get();                
            if ($empty_lots) $temp_arr = $empty_lots->pluck('tracking_main')->toArray();

            $first_lot = PhilIndWorksheet::where([
                ['pallet_number',$old_pallet],
                ['lot', '<>',null]
            ])->first();           

            if ($temp_arr && $first_lot) {

                $result = $this->checkMaxLotEn($old_pallet, $first_lot->lot);
                $lots = $result[0];            
                $other_lots = $result[1];           
                $empty_lots = $result[2];
                $lot = $result[3];
                
                if ($tracking) {
                    while (($i = array_search($tracking, $temp_arr)) !== false) {
                        unset($temp_arr[$i]);
                    }
                    $temp = [];
                    foreach ($temp_arr as $key => $value) {
                        $temp[] = $value;
                    }
                    $temp_arr = $temp;
                }

                if ($temp_arr && $lots->count() > 3) {
                    $other_track_arr = implode(",", $temp_arr);
                    $notifications_pallet = json_decode($notifications->pallet);
                    if (is_object($notifications_pallet)) {
                        $notifications_pallet->empty_arr = 'CHANGE PALLET NO. FOR THE MISSING PARCELS ('.$other_track_arr.')';
                    }
                    else{
                        $notifications_pallet = (object)['other_arr'=>'','empty_arr'=>''];
                    }
                    $notifications->pallet = json_encode($notifications_pallet);                  
                }
                else{
                    $notifications_pallet = json_decode($notifications->pallet);
                    if (is_object($notifications_pallet)) {
                        $notifications_pallet->empty_arr = '';
                    }
                    else{
                        $notifications_pallet = (object)['other_arr'=>'','empty_arr'=>''];
                    }
                    $notifications->pallet = json_encode($notifications_pallet);
                }
            } 
            elseif ($warehouse) {
                $notifications_pallet = json_decode($notifications->pallet);
                if (is_object($notifications_pallet)) {
                    $notifications_pallet->empty_arr = '';
                }
                else{
                    $notifications_pallet = (object)['other_arr'=>'','empty_arr'=>''];
                }
                $notifications->pallet = json_encode($notifications_pallet);
            } 
        }                     
        
        $notifications = json_encode($notifications);
        Warehouse::where('pallet',$old_pallet)->update([
            'notifications' => $notifications
        ]);

        return true;
    }


    private function checkLotRu($lot, $pallet)
    {     
        if ($lot) {
            $result = $this->checkMaxLotRu($pallet, $lot);
        }
        else{           
            $first_lot = NewWorksheet::where([
                ['pallet_number',$pallet],
                ['batch_number', '<>',null]
            ])->first();           

            if ($first_lot) {
                $result = $this->checkMaxLotRu($pallet, $first_lot->batch_number);                
            }
            else {
                $lots = NewWorksheet::where([
                    ['pallet_number',$pallet],
                    ['lot',null]
                ])->get();
                $empty_lots = $lots;
                $result = [$lots, collect([]), $empty_lots, $lot];
            }
        }        
                                
        return $result;
    }


    private function checkLotEn($lot, $pallet)
    {           
        if ($lot) {
            $result = $this->checkMaxLotEn($pallet, $lot);
        }
        else{           
            $first_lot = PhilIndWorksheet::where([
                ['pallet_number',$pallet],
                ['lot', '<>',null]
            ])->first();           

            if ($first_lot) {
                $result = $this->checkMaxLotEn($pallet, $first_lot->lot);                
            }
            else {
                $lots = PhilIndWorksheet::where([
                    ['pallet_number',$pallet],
                    ['lot',null]
                ])->get();
                $empty_lots = $lots;
                $result = [$lots, collect([]), $empty_lots, $lot];
            }
        }        
                                
        return $result;
    }


    protected function updateWarehouseLot($tracking, $lot, $which_admin, $old_pallet = false, $old_lot = false)
    {        
        $notifications = (object)['pallet'=>'','tracking'=>''];
        $other_track_arr = '';
        $empty_track_arr = '';
        $empty_lots_count = 0;

        if ($which_admin === 'ru') {
            $worksheet = NewWorksheet::where('tracking_main',$tracking)->first();
            $pallets = NewWorksheet::where('pallet_number',$worksheet->pallet_number)->get();

            $result = $this->checkLotRu($lot, $worksheet->pallet_number);
            $lots = $result[0];            
            $other_lots = $result[1];           
            $empty_lots = $result[2];
            $lot = $result[3];
            $different_lots = $pallets->count() - $lots->count();                         
        }
        elseif ($which_admin === 'en') {
            $worksheet = PhilIndWorksheet::where('tracking_main',$tracking)->first();
            $pallets = PhilIndWorksheet::where('pallet_number',$worksheet->pallet_number)->get();

            $result = $this->checkLotEn($lot, $worksheet->pallet_number);
            $lots = $result[0];            
            $other_lots = $result[1];           
            $empty_lots = $result[2];
            $lot = $result[3];
            $different_lots = $pallets->count() - $lots->count();                                    
        }

        $empty_lots_count = $empty_lots->count();

        //dd([$pallets->count(),$lots->count(),$other_lots->count(),$empty_lots->count(),$lot]);

        // Updating of notifications for new pallet
        if ($other_lots) $temp_arr = $other_lots->pluck('tracking_main')->toArray();
        if ($temp_arr) $other_track_arr = implode(",", $temp_arr);
        if ($empty_lots) $temp_arr = $empty_lots->pluck('tracking_main')->toArray();
        if ($temp_arr) $empty_track_arr = implode(",", $temp_arr);

        $warehouse = Warehouse::where('pallet',$worksheet->pallet_number)->first();
        if ($warehouse) {
            if($warehouse->notifications){
                $notifications = json_decode($warehouse->notifications);
            }
        }            

        if ($pallets->count() === $lots->count()) {
            $notifications->pallet = '';
            $notifications = json_encode($notifications); 
            if ($lot) {
                Warehouse::where('pallet',$worksheet->pallet_number)->update([
                    'lot' => $lot,
                    'left' => date('Y-m-d'),
                    'notifications' => $notifications
                ]);
            } 
            else{
                Warehouse::where('pallet',$worksheet->pallet_number)->update([
                    'lot' => null,
                    'left' => null,
                    'notifications' => $notifications
                ]);
            }                          
        }
        elseif ($lots->count() > 3) {            
            $notifications_pallet = json_decode($notifications->pallet);
            if (!$notifications_pallet) $notifications_pallet = (object)['other_arr'=>'','empty_arr'=>''];
            elseif (!is_object($notifications_pallet)) $notifications_pallet = (object)['other_arr'=>'','empty_arr'=>''];
            if ($empty_track_arr) $notifications_pallet->empty_arr = 'CHANGE PALLET NO. FOR THE MISSING PARCELS ('.$empty_track_arr.')';
            else $notifications_pallet->empty_arr = '';
            if ($other_track_arr) $notifications_pallet->other_arr = 'CHANGE OR DELETE PALLET NO. FOR THE PARCELS ('.$other_track_arr.') AND TRY AGAIN';
            else $notifications_pallet->other_arr = '';

            $notifications->pallet = json_encode($notifications_pallet);
            $notifications = json_encode($notifications);
            if ($lot) {
                Warehouse::where('pallet',$worksheet->pallet_number)->update([
                    'lot' => $lot,
                    'left' => date('Y-m-d'),
                    'notifications' => $notifications
                ]);
            } 
            else{
                Warehouse::where('pallet',$worksheet->pallet_number)->update([
                    'lot' => null,
                    'left' => null,
                    'notifications' => $notifications
                ]);
            } 
        } 

        // Updating of notifications for old pallet
        if ($which_admin === 'ru') {           
            if ($old_pallet && $old_lot) {
                $this->updateNotificationsRu($old_pallet, $old_lot);
            }
            if ($empty_lots_count && $old_pallet) {
                $this->updateNotificationsRu($old_pallet, null);
            }
        }
        elseif ($which_admin === 'en'){
            if ($old_pallet && $old_lot) {
                $this->updateNotificationsEn($old_pallet, $old_lot);
            }
            if ($empty_lots_count && $old_pallet) {
                $this->updateNotificationsEn($old_pallet, null);
            }
        }
    }


    protected function checkForMissingTracking($tracking)
    {
        $result = Warehouse::where('notifications', 'like', '%'.$tracking.'%')->first();
        if ($result) {
            $notifications = json_decode($result->notifications);
            if ($notifications->tracking) {
                $notifications_tracking = json_decode($notifications->tracking);
                if (stripos($notifications_tracking->arr, $tracking) !== false) {
                    $notifications_tracking->arr = str_replace(",$tracking", "", $notifications_tracking->arr);
                    $notifications_tracking->arr = str_replace("$tracking,", "", $notifications_tracking->arr);
                    $notifications_tracking->arr = str_replace($tracking, "", $notifications_tracking->arr);
                    if ($notifications_tracking->arr) {
                        $notifications_tracking->message = 'The ('.$notifications_tracking->arr.') are missing in the work sheet. Check the tracking number or add it to the work sheet';
                    }
                    else{
                        $notifications_tracking->message = '';
                    }
                }

                $notifications_tracking = json_encode($notifications_tracking);     
                $notifications->tracking = $notifications_tracking;     
                $notifications = json_encode($notifications);
                $result->notifications = $notifications;
                $result->save();
            }
            if ((stripos($tracking, 'CD') !== false) || (stripos($tracking, 'BL') !== false)){                   
                NewWorksheet::where('tracking_main', $tracking)
                ->update([
                    'pallet_number' => $result->pallet,
                    'batch_number' => $result->lot
                ]);
            }
            else if ((stripos($tracking, 'IN') !== false) || (stripos($tracking, 'NE') !== false)){
                PhilIndWorksheet::where('tracking_main', $tracking)
                ->update([
                    'pallet_number' => $result->pallet,
                    'lot' => $result->lot
                ]);
            }
        }
        return true;
    }


    protected function updateWarehousePallet($old_tracking, $new_tracking, $old_pallet, $new_pallet, $old_lot, $new_lot, $which_admin, $worksheet)
    {
        $message = '';
        if ($old_tracking !== $new_tracking) {
            $update_result = $this->updateWarehouse($old_pallet, $new_pallet, $old_tracking, $new_tracking);
            if (!$update_result) {
                $worksheet->pallet_number = $old_pallet;
                $worksheet->save();
                $message = 'Pallet number is not correct!';
            }
        }
        else{
            $update_result = $this->updateWarehouse($old_pallet, $new_pallet, $old_tracking);
            if (!$update_result) {
                $worksheet->pallet_number = $old_pallet;
                $worksheet->save();
                $message = 'Pallet number is not correct!';
            }
            if ($old_pallet) {
                $this->updateWarehouseLot($old_tracking, $new_lot, $which_admin, $old_pallet, $old_lot);
            }                       
        }               
        return $message;
    }


    public function removeTrackingFromPalletWorksheet($id, $which_admin)
    {
        if ($which_admin === 'ru') {
            $worksheet = NewWorksheet::find($id);
            $tracking = $worksheet->tracking_main;
            if ($tracking) {
                $result = Warehouse::where('tracking_numbers', 'like', '%'.$tracking.'%')->first();
                if ($result) {
                    $pallet = $result->pallet;                    
                    $this->updateWarehouseWorksheet($pallet, $tracking);
                }
            }
        }
        elseif ($which_admin === 'en') {
            $worksheet = PhilIndWorksheet::find($id);
            $tracking = $worksheet->tracking_main;
            if ($tracking) {
                $result = Warehouse::where('tracking_numbers', 'like', '%'.$tracking.'%')->first();
                if ($result) {
                    $pallet = $result->pallet;
                    $this->updateWarehouseWorksheet($pallet, $tracking);
                }
            }
        }
        
        return true;
    }


}
