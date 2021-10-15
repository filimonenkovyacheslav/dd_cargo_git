<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\NewWorksheet;
use App\PhilIndWorksheet;
use App\Receipt;
use App\ReceiptArchive;
//use App\Warehouse;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


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


/*    protected function updateWarehouse($old_pallet, $new_pallet, $old_tracking, $new_tracking = null)
    {
        $track_arr = [];
        $result = Warehouse::where('pallet', $new_pallet)->first();

        $tracking_main = ($new_tracking)?$new_tracking:$old_tracking;

        // Adding tracking to pallet
        if ($result) {
            $track_arr = json_decode($result->tracking_numbers);
            $track_arr[] = $tracking_main;
            $track_arr = json_encode($track_arr);
            $result->tracking_numbers = $track_arr;
            $result->save();
        }
        else{
            $track_arr[] = $tracking_main;
            $track_arr = json_encode($track_arr);
            Warehouse::create([
                'pallet' => $new_pallet,
                'tracking_numbers' => $track_arr
            ]);
        }

        // Removing tracking from old pallet
        if ($old_pallet) {
            $old_result = Warehouse::where('pallet', $old_pallet)->first();
            if ($old_result) {
                $track_arr = json_decode($old_result->tracking_numbers);
                while (($i = array_search($tracking_main, $track_arr)) !== false) {
                    unset($track_arr[$i]);
                }
                $track_arr = json_encode($track_arr);
                $old_result->tracking_numbers = $track_arr;
                $old_result->save();
                if (!$track_arr) Warehouse::where('pallet', $old_pallet)->delete(); 
            }                   
        }

        // Removing old tracking from pallet
        if ($old_pallet && $old_tracking && $new_tracking) {
            $old_result = Warehouse::where('pallet', $old_pallet)->first();
            if ($old_result) {
                $track_arr = json_decode($old_result->tracking_numbers);
                while (($i = array_search($old_tracking, $track_arr)) !== false) {
                    unset($track_arr[$i]);
                }
                $track_arr = json_encode($track_arr);
                $old_result->tracking_numbers = $track_arr;
                $old_result->save();
                if (!$track_arr) Warehouse::where('pallet', $old_pallet)->delete(); 
            }                   
        }
    }*/

}
