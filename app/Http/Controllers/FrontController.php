<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\NewWorksheet;
use App\ChinaWorksheet;
use App\PhilIndWorksheet;
use App\PackingSea;
use App\PackingEng;
use App\Http\Controllers\Admin\AdminController;
use App\DraftWorksheet;
use App\EngDraftWorksheet;
use DB;


class FrontController extends AdminController
{
    public function index()
    {
               
    }


    public function parcelForm()
    {
        return view('parcel_form');       
    }


    public function newParcelAdd(Request $request)
    {        
        $fields = $this->getTableColumns('draft_worksheet'); 

        $new_worksheet = new DraftWorksheet();       

        foreach($fields as $field){
            
            if ($field === 'sender_name') {
                $new_worksheet->$field = $request->input('first_name').' '.$request->input('last_name');
            }
            else if($field === 'site_name'){
                $new_worksheet->$field = 'DD-C';
            }
            else if($field === 'recipient_name'){
                $new_worksheet->$field = $request->input('recipient_first_name').' '.$request->input('recipient_last_name');
            }
            else if($field === 'package_content'){
                $content = '';
                if ($request->input('clothing_quantity')) {
                    $content .= 'Одежда: '.$request->input('clothing_quantity').'; ';
                }
                if ($request->input('shoes_quantity')) {
                    $content .= 'Обувь: '.$request->input('shoes_quantity').'; ';
                }               
                if ($request->input('other_content_1')) {
                    $content .= $request->input('other_content_1').': '.$request->input('other_quantity_1').'; ';
                }
                if ($request->input('other_content_2')) {
                    $content .= $request->input('other_content_2').': '.$request->input('other_quantity_2').'; ';
                }
                if ($request->input('other_content_3')) {
                    $content .= $request->input('other_content_3').': '.$request->input('other_quantity_3').'; ';
                }
                if ($request->input('other_content_4')) {
                    $content .= $request->input('other_content_4').': '.$request->input('other_quantity_4').'; ';
                }
                if ($request->input('other_content_5')) {
                    $content .= $request->input('other_content_5').': '.$request->input('other_quantity_5').'; ';
                }
                if ($request->input('other_content_6')) {
                    $content .= $request->input('other_content_6').': '.$request->input('other_quantity_6').'; ';
                }
                if ($request->input('other_content_7')) {
                    $content .= $request->input('other_content_7').': '.$request->input('other_quantity_7').'; ';
                }
                if ($request->input('other_content_8')) {
                    $content .= $request->input('other_content_8').': '.$request->input('other_quantity_8').'; ';
                }
                
                $new_worksheet->$field = trim($content);
            }
            else if ($field === 'comment_2'){
                $new_worksheet->$field = $request->input('need_box');
            }
            else if ($field !== 'created_at'){
                $new_worksheet->$field = $request->input($field);
            }           
        }

        $new_worksheet->date = date('Y.m.d');
        $new_worksheet->status = 'Забрать';
        $work_sheet_id = $new_worksheet->id;

        if ($new_worksheet->save()){

            $message = 'Заказ посылки успешно создан !';
                       
            // Packing
            $fields_packing = ['payer', 'contract', 'type', 'track_code', 'full_shipper', 'full_consignee', 'country_code', 'postcode', 'region', 'district', 'city', 'street', 'house', 'body', 'room', 'phone', 'tariff', 'tariff_cent', 'weight_kg', 'weight_g', 'service_code', 'amount_1', 'amount_2', 'attachment_number', 'attachment_name', 'amount_3', 'weight_enclosures_kg', 'weight_enclosures_g', 'value_euro', 'value_cent', 'work_sheet_id'];
            $j=1;

            if ($request->input('clothing_quantity')) {
                $packing_sea = new PackingSea();
                foreach($fields_packing as $field){
                    if ($field === 'type') {
                        $packing_sea->$field = $request->input('tariff');
                    }
                    else if ($field === 'full_shipper') {
                        $packing_sea->$field = $request->input('first_name').' '.$request->input('last_name');
                    }
                    else if ($field === 'full_consignee') {
                        $packing_sea->$field = $request->input('recipient_first_name').' '.$request->input('recipient_last_name');
                    }
                    else if ($field === 'country_code') {
                        $packing_sea->$field = $request->input('recipient_country');
                    }
                    else if ($field === 'postcode') {
                        $packing_sea->$field = $request->input('recipient_postcode');
                    }
                    else if ($field === 'city') {
                        $packing_sea->$field = $request->input('recipient_city');
                    }
                    else if ($field === 'street') {
                        $packing_sea->$field = $request->input('recipient_street');
                    }
                    else if ($field === 'house') {
                        $packing_sea->$field = $request->input('recipient_house');
                    }
                    else if ($field === 'room') {
                        $packing_sea->$field = $request->input('recipient_room');
                    }
                    else if ($field === 'phone') {
                        $packing_sea->$field = $request->input('recipient_phone');
                    }
                    else if ($field === 'tariff') {
                        $packing_sea->$field = null;
                    }
                    else if ($field === 'work_sheet_id') {
                        $packing_sea->$field = $work_sheet_id;
                    }
                    else if ($field === 'attachment_number') {
                        $packing_sea->$field = $j;
                    }
                    else if ($field === 'attachment_name') {
                        $packing_sea->$field = 'Одежда';
                    }
                    else if ($field === 'amount_3') {
                        $packing_sea->$field = $request->input('clothing_quantity');
                    }
                    else{
                        $packing_sea->$field = $request->input($field);
                    }
                }
                $j++;
                $packing_sea->save();
            }

            if ($request->input('shoes_quantity')) {
                $packing_sea = new PackingSea();
                foreach($fields_packing as $field){
                    if ($field === 'type') {
                        $packing_sea->$field = $request->input('tariff');
                    }
                    else if ($field === 'full_shipper') {
                        $packing_sea->$field = $request->input('first_name').' '.$request->input('last_name');
                    }
                    else if ($field === 'full_consignee') {
                        $packing_sea->$field = $request->input('recipient_first_name').' '.$request->input('recipient_last_name');
                    }
                    else if ($field === 'country_code') {
                        $packing_sea->$field = $request->input('recipient_country');
                    }
                    else if ($field === 'postcode') {
                        $packing_sea->$field = $request->input('recipient_postcode');
                    }
                    else if ($field === 'city') {
                        $packing_sea->$field = $request->input('recipient_city');
                    }
                    else if ($field === 'street') {
                        $packing_sea->$field = $request->input('recipient_street');
                    }
                    else if ($field === 'house') {
                        $packing_sea->$field = $request->input('recipient_house');
                    }
                    else if ($field === 'room') {
                        $packing_sea->$field = $request->input('recipient_room');
                    }
                    else if ($field === 'phone') {
                        $packing_sea->$field = $request->input('recipient_phone');
                    }
                    else if ($field === 'tariff') {
                        $packing_sea->$field = null;
                    }
                    else if ($field === 'work_sheet_id') {
                        $packing_sea->$field = $work_sheet_id;
                    }
                    else if ($field === 'attachment_number') {
                        $packing_sea->$field = $j;
                    }
                    else if ($field === 'attachment_name') {
                        $packing_sea->$field = 'Обувь';
                    }
                    else if ($field === 'amount_3') {
                        $packing_sea->$field = $request->input('shoes_quantity');
                    }
                    else{
                        $packing_sea->$field = $request->input($field);
                    }
                }
                $j++;
                $packing_sea->save();
            }

            for ($i=1; $i < 9; $i++) { 
                if ($request->input('other_content_'.$i)) {
                    $packing_sea = new PackingSea();
                    foreach($fields_packing as $field){
                        if ($field === 'type') {
                            $packing_sea->$field = $request->input('tariff');
                        }
                        else if ($field === 'full_shipper') {
                            $packing_sea->$field = $request->input('first_name').' '.$request->input('last_name');
                        }
                        else if ($field === 'full_consignee') {
                            $packing_sea->$field = $request->input('recipient_first_name').' '.$request->input('recipient_last_name');
                        }
                        else if ($field === 'country_code') {
                            $packing_sea->$field = $request->input('recipient_country');
                        }
                        else if ($field === 'postcode') {
                            $packing_sea->$field = $request->input('recipient_postcode');
                        }
                        else if ($field === 'city') {
                            $packing_sea->$field = $request->input('recipient_city');
                        }
                        else if ($field === 'street') {
                            $packing_sea->$field = $request->input('recipient_street');
                        }
                        else if ($field === 'house') {
                            $packing_sea->$field = $request->input('recipient_house');
                        }
                        else if ($field === 'room') {
                            $packing_sea->$field = $request->input('recipient_room');
                        }
                        else if ($field === 'phone') {
                            $packing_sea->$field = $request->input('recipient_phone');
                        }
                        else if ($field === 'tariff') {
                            $packing_sea->$field = null;
                        }
                        else if ($field === 'work_sheet_id') {
                            $packing_sea->$field = $work_sheet_id;
                        }
                        else if ($field === 'attachment_number') {
                            $packing_sea->$field = $j;
                        }
                        else if ($field === 'attachment_name') {
                            $packing_sea->$field = $request->input('other_content_'.$i);
                        }
                        else if ($field === 'amount_3') {
                            $packing_sea->$field = $request->input('other_quantity_'.$i);
                        }
                        else{
                            $packing_sea->$field = $request->input($field);
                        }
                    }
                    $j++;
                    $packing_sea->save();
                }
            } 
        }
        else{
            $message = 'Ошибка сохранения !';
        }        
        
        return redirect()->route('parcelForm')->with('status', $message);        
    }


    public function forwardParcelAdd(Request $request)
    {
        if ($request->input('url_name')) {
            $new_worksheet = new DraftWorksheet();
            $fields = $this->getTableColumns('draft_worksheet');        

            foreach($fields as $field){
                if ($field === 'sender_name') {
                    $new_worksheet->$field = $request->input('first_name').' '.$request->input('last_name');
                }
                else if($field === 'site_name'){
                    $new_worksheet->$field = 'For';
                }
                else if($field === 'recipient_name'){
                    $new_worksheet->$field = $request->input('recipient_first_name').' '.$request->input('recipient_last_name');
                }
                else if($field === 'package_content'){
                    $content = '';
                    if ($request->input('clothing_quantity')) {
                        $content .= 'Одежда: '.$request->input('clothing_quantity').'; ';
                    }
                    if ($request->input('shoes_quantity')) {
                        $content .= 'Обувь: '.$request->input('shoes_quantity').'; ';
                    }
                    if ($request->input('other_content_1')) {
                        $content .= $request->input('other_content_1').': '.$request->input('other_quantity_1').'; ';
                    }
                    if ($request->input('other_content_2')) {
                        $content .= $request->input('other_content_2').': '.$request->input('other_quantity_2').'; ';
                    }
                    if ($request->input('other_content_3')) {
                        $content .= $request->input('other_content_3').': '.$request->input('other_quantity_3').'; ';
                    }
                    if ($request->input('other_content_4')) {
                        $content .= $request->input('other_content_4').': '.$request->input('other_quantity_4').'; ';
                    }
                    if ($request->input('other_content_5')) {
                        $content .= $request->input('other_content_5').': '.$request->input('other_quantity_5').'; ';
                    }
                    if ($request->input('other_content_6')) {
                        $content .= $request->input('other_content_6').': '.$request->input('other_quantity_6').'; ';
                    }
                    if ($request->input('other_content_7')) {
                        $content .= $request->input('other_content_7').': '.$request->input('other_quantity_7').'; ';
                    }
                    if ($request->input('other_content_8')) {
                        $content .= $request->input('other_content_8').': '.$request->input('other_quantity_8').'; ';
                    }

                    $new_worksheet->$field = trim($content);
                }
                else if ($field === 'comment_2'){
                    $new_worksheet->$field = $request->input('need_box');
                }
                else if ($field !== 'created_at'){
                    $new_worksheet->$field = $request->input($field);
                }           
            }

            $new_worksheet->date = date('Y.m.d');
            $new_worksheet->status = 'Забрать';
            $work_sheet_id = $new_worksheet->id;

            if($new_worksheet->save()){
                $message = 'Заказ посылки успешно создан !';

                // Packing
                $fields_packing = ['payer', 'contract', 'type', 'track_code', 'full_shipper', 'full_consignee', 'country_code', 'postcode', 'region', 'district', 'city', 'street', 'house', 'body', 'room', 'phone', 'tariff', 'tariff_cent', 'weight_kg', 'weight_g', 'service_code', 'amount_1', 'amount_2', 'attachment_number', 'attachment_name', 'amount_3', 'weight_enclosures_kg', 'weight_enclosures_g', 'value_euro', 'value_cent', 'work_sheet_id'];
                $j=1;

                if ($request->input('clothing_quantity')) {
                    $packing_sea = new PackingSea();
                    foreach($fields_packing as $field){
                        if ($field === 'type') {
                            $packing_sea->$field = $request->input('tariff');
                        }
                        else if ($field === 'full_shipper') {
                            $packing_sea->$field = $request->input('first_name').' '.$request->input('last_name');
                        }
                        else if ($field === 'full_consignee') {
                            $packing_sea->$field = $request->input('recipient_first_name').' '.$request->input('recipient_last_name');
                        }
                        else if ($field === 'country_code') {
                            $packing_sea->$field = $request->input('recipient_country');
                        }
                        else if ($field === 'postcode') {
                            $packing_sea->$field = $request->input('recipient_postcode');
                        }
                        else if ($field === 'city') {
                            $packing_sea->$field = $request->input('recipient_city');
                        }
                        else if ($field === 'street') {
                            $packing_sea->$field = $request->input('recipient_street');
                        }
                        else if ($field === 'house') {
                            $packing_sea->$field = $request->input('recipient_house');
                        }
                        else if ($field === 'room') {
                            $packing_sea->$field = $request->input('recipient_room');
                        }
                        else if ($field === 'phone') {
                            $packing_sea->$field = $request->input('recipient_phone');
                        }
                        else if ($field === 'tariff') {
                            $packing_sea->$field = null;
                        }
                        else if ($field === 'work_sheet_id') {
                            $packing_sea->$field = $work_sheet_id;
                        }
                        else if ($field === 'attachment_number') {
                            $packing_sea->$field = $j;
                        }
                        else if ($field === 'attachment_name') {
                            $packing_sea->$field = 'Одежда';
                        }
                        else if ($field === 'amount_3') {
                            $packing_sea->$field = $request->input('clothing_quantity');
                        }
                        else{
                            $packing_sea->$field = $request->input($field);
                        }
                    }
                    $j++;
                    $packing_sea->save();
                }

                if ($request->input('shoes_quantity')) {
                    $packing_sea = new PackingSea();
                    foreach($fields_packing as $field){
                        if ($field === 'type') {
                            $packing_sea->$field = $request->input('tariff');
                        }
                        else if ($field === 'full_shipper') {
                            $packing_sea->$field = $request->input('first_name').' '.$request->input('last_name');
                        }
                        else if ($field === 'full_consignee') {
                            $packing_sea->$field = $request->input('recipient_first_name').' '.$request->input('recipient_last_name');
                        }
                        else if ($field === 'country_code') {
                            $packing_sea->$field = $request->input('recipient_country');
                        }
                        else if ($field === 'postcode') {
                            $packing_sea->$field = $request->input('recipient_postcode');
                        }
                        else if ($field === 'city') {
                            $packing_sea->$field = $request->input('recipient_city');
                        }
                        else if ($field === 'street') {
                            $packing_sea->$field = $request->input('recipient_street');
                        }
                        else if ($field === 'house') {
                            $packing_sea->$field = $request->input('recipient_house');
                        }
                        else if ($field === 'room') {
                            $packing_sea->$field = $request->input('recipient_room');
                        }
                        else if ($field === 'phone') {
                            $packing_sea->$field = $request->input('recipient_phone');
                        }
                        else if ($field === 'tariff') {
                            $packing_sea->$field = null;
                        }
                        else if ($field === 'work_sheet_id') {
                            $packing_sea->$field = $work_sheet_id;
                        }
                        else if ($field === 'attachment_number') {
                            $packing_sea->$field = $j;
                        }
                        else if ($field === 'attachment_name') {
                            $packing_sea->$field = 'Обувь';
                        }
                        else if ($field === 'amount_3') {
                            $packing_sea->$field = $request->input('shoes_quantity');
                        }
                        else{
                            $packing_sea->$field = $request->input($field);
                        }
                    }
                    $j++;
                    $packing_sea->save();
                }

                for ($i=1; $i < 9; $i++) { 
                    if ($request->input('other_content_'.$i)) {
                        $packing_sea = new PackingSea();
                        foreach($fields_packing as $field){
                            if ($field === 'type') {
                                $packing_sea->$field = $request->input('tariff');
                            }
                            else if ($field === 'full_shipper') {
                                $packing_sea->$field = $request->input('first_name').' '.$request->input('last_name');
                            }
                            else if ($field === 'full_consignee') {
                                $packing_sea->$field = $request->input('recipient_first_name').' '.$request->input('recipient_last_name');
                            }
                            else if ($field === 'country_code') {
                                $packing_sea->$field = $request->input('recipient_country');
                            }
                            else if ($field === 'postcode') {
                                $packing_sea->$field = $request->input('recipient_postcode');
                            }
                            else if ($field === 'city') {
                                $packing_sea->$field = $request->input('recipient_city');
                            }
                            else if ($field === 'street') {
                                $packing_sea->$field = $request->input('recipient_street');
                            }
                            else if ($field === 'house') {
                                $packing_sea->$field = $request->input('recipient_house');
                            }
                            else if ($field === 'room') {
                                $packing_sea->$field = $request->input('recipient_room');
                            }
                            else if ($field === 'phone') {
                                $packing_sea->$field = $request->input('recipient_phone');
                            }
                            else if ($field === 'tariff') {
                                $packing_sea->$field = null;
                            }
                            else if ($field === 'work_sheet_id') {
                                $packing_sea->$field = $work_sheet_id;
                            }
                            else if ($field === 'attachment_number') {
                                $packing_sea->$field = $j;
                            }
                            else if ($field === 'attachment_name') {
                                $packing_sea->$field = $request->input('other_content_'.$i);
                            }
                            else if ($field === 'amount_3') {
                                $packing_sea->$field = $request->input('other_quantity_'.$i);
                            }
                            else{
                                $packing_sea->$field = $request->input($field);
                            }
                        }
                        $j++;
                        $packing_sea->save();
                    }
                }
            }
            else{
                $message = 'Ошибка формы !';
            }                       

            return redirect($request->input('url_name').'?message='.$message);
        }

    }


    public function trackingForm()
    {
        return view('tracking_form');        
    }


    public function getTracking(Request $request)
    {
        $tracking = $request->input('get_tracking');
        $message_arr['ru'] = '';
        $message_arr['en'] = '';
        $message_arr['he'] = '';
        $message_arr['ua'] = '';

        $update_status_date = NewWorksheet::where('update_status_date','=', date('Y-m-d'))->get()->count();
        
        if ($update_status_date === 0) {
            app()->call('App\Http\Controllers\RuPostalTrackingController@updateStatusFromUser', [$tracking]);
        }       

        $row = DB::table('worksheet')
            ->select('status','guarantee_text_en','guarantee_text_he','guarantee_text_ua')
            ->where('tracking', '=', $tracking)
            ->get();

        if ($row->count()) {
            foreach ($row as $val) {
                if ($val->status) {
                    $message_arr['ru'] = $val->status;
                }
                if ($val->status) {
                    $message_arr['en'] = $val->guarantee_text_en;
                }
                if ($val->status) {
                    $message_arr['he'] = $val->guarantee_text_he;
                }
                if ($val->status) {
                    $message_arr['ua'] = $val->guarantee_text_ua;
                }
            }
        }
        else{
            $row = DB::table('new_worksheet')
            ->select('status','status_en','status_he','status_ua')
            ->where([
                ['tracking_main', '=', $tracking],
                ['site_name', '=', 'DD-C']
            ])
            ->orWhere([
                ['tracking_main', 'like', '%'.', '.$tracking],
                ['site_name', '=', 'DD-C']
            ])
            ->orWhere([
                ['tracking_main', 'like', '%'.$tracking.', '.'%'],
                ['site_name', '=', 'DD-C']
            ])
            ->get();
            if ($row->count()) {
                foreach ($row as $val) {
                    if ($val->status) {
                        $message_arr['ru'] = $val->status;
                    }
                    if ($val->status) {
                        $message_arr['en'] = $val->status_en;
                    }
                    if ($val->status) {
                        $message_arr['he'] = $val->status_he;
                    }
                    if ($val->status) {
                        $message_arr['ua'] = $val->status_ua;
                    }
                }
            }
        }
        
        if (!$row->count()) {            
            $row = DB::table('china_worksheet')
            ->select('status','status_he','status_ru')
            ->where('tracking_main', '=', $tracking)
            ->orWhere('tracking_main', 'like', '%'.', '.$tracking)
            ->orWhere('tracking_main', 'like', '%'.$tracking.', '.'%')
            ->get();
            if ($row->count()) {
                foreach ($row as $val) {
                    if ($val->status) {
                        $message_arr['ru'] = $val->status_ru;
                    }
                    if ($val->status) {
                        $message_arr['en'] = $val->status;
                    }
                    if ($val->status) {
                        $message_arr['he'] = $val->status_he;
                    }
                }
            }
            $message_arr['ua'] = '';
        }

        if (!$row->count()) {            
            $row = DB::table('phil_ind_worksheet')
            ->select('status','status_he','status_ru')
            ->where('tracking_main', '=', $tracking)
            ->orWhere('tracking_main', 'like', '%'.', '.$tracking)
            ->orWhere('tracking_main', 'like', '%'.$tracking.', '.'%')
            ->get();
            if ($row->count()) {
                foreach ($row as $val) {
                    if ($val->status) {
                        $message_arr['ru'] = $val->status_ru;
                    }
                    if ($val->status) {
                        $message_arr['en'] = $val->status;
                    }
                    if ($val->status) {
                        $message_arr['he'] = $val->status_he;
                    }
                }
            }
            $message_arr['ua'] = '';
        }         
        
        return redirect()->route('trackingForm')
        ->with( 'message_ru', $message_arr['ru'] )
        ->with( 'message_en', $message_arr['en'] )
        ->with( 'message_he', $message_arr['he'] )
        ->with( 'message_ua', $message_arr['ua'] )
        ->with( 'not_found', 'not_found' )
        ->with( 'update_status_date', $update_status_date );        
    }


    public function getForwardTracking(Request $request)
    {
        //dd($request->input('get_tracking'));
        $tracking = $request->input('get_tracking');
        $message_arr['ru'] = '';
        $message_arr['en'] = '';
        $message_arr['he'] = '';
        $message_arr['ua'] = '';
        $message = 'Не найдено !';

        $update_status_date = NewWorksheet::where('update_status_date','=', date('Y-m-d'))->get()->count();

        if ($update_status_date === 0) {
            app()->call('App\Http\Controllers\RuPostalTrackingController@updateStatusFromUser', [$tracking]);
        }
        
        $row = DB::table('new_worksheet')
        ->select('status','status_en','status_he','status_ua')
        ->where([
            ['tracking_main', '=', $tracking],
            ['site_name', '=', 'For']
        ])
        ->orWhere([
            ['tracking_main', 'like', '%'.', '.$tracking],
            ['site_name', '=', 'For']
        ])
        ->orWhere([
            ['tracking_main', 'like', '%'.$tracking.', '.'%'],
            ['site_name', '=', 'For']
        ])
        ->get();
        if ($row->count()) {
            foreach ($row as $val) {
                if ($val->status) {
                    $message_arr['ru'] = $val->status;
                }
                if ($val->status) {
                    $message_arr['en'] = $val->status_en;
                }
                if ($val->status) {
                    $message_arr['he'] = $val->status_he;
                }
                if ($val->status) {
                    $message_arr['ua'] = $val->status_ua;
                }
            }

            $message = $message_arr['ru'];
        } 

        if ($request->input('url_name')) {
            return redirect($request->input('url_name').'?message='.$message.'&update_status_date='.$update_status_date);
        }
        else{
            return response()->json($message_arr, 200);
        }                                           
    }


    public function chinaParcelForm()
    {
        return view('china_parcel_form');        
    }


    public function chinaParcelAdd(Request $request)
    {
        $china_worksheet = new ChinaWorksheet();
        $fields = ['date', 'tracking_main', 'tracking_local', 'status', 'customer_name', 'customer_address', 'customer_phone', 'customer_email', 'supplier_name', 'supplier_address', 'supplier_phone', 'supplier_email', 'shipment_description', 'weight', 'length', 'width', 'height', 'lot_number', 'status_he', 'status_ru'];
        
        foreach($fields as $field){           
            $china_worksheet->$field = $request->input($field);          
        }

        $china_worksheet->save();

        $message = 'Shipment order successfully created !';
        
        return redirect()->route('chinaParcelForm')->with('status', $message);        
    }


    public function philIndParcelForm()
    {
        return view('phil_ind_parcel_form');        
    }


    public function philIndParcelAdd(Request $request)
    {
        $worksheet = new EngDraftWorksheet();
        $fields = $this->getTableColumns('eng_draft_worksheet');
        
        foreach($fields as $field){
            if ($field === 'shipper_name') {
                $worksheet->$field = $request->input('first_name').' '.$request->input('last_name');
            }
            else if ($field === 'consignee_name') {
                $worksheet->$field = $request->input('consignee_first_name').' '.$request->input('consignee_last_name');
            }
            else if ($field === 'consignee_address') {
                $worksheet->$field = $request->input('consignee_country').' '.$request->input('consignee_address');
            }
            else if ($field === 'shipped_items') {
                $temp = '';
                for ($i=1; $i < 11; $i++) { 
                    if (null !== $request->input('item_'.$i)) {
                        $temp .= $request->input('item_'.$i).' - '.$request->input('q_item_'.$i).'; ';
                    }
                }
                $worksheet->$field = $temp;
            }
            else if ($field !== 'created_at'){
                $worksheet->$field = $request->input($field);
            }                               
        }

        if (!$worksheet->date) {
            $worksheet->date = date('Y-m-d');
        }        
        $worksheet->status = 'Pick up';
        $work_sheet_id = $worksheet->id;

        if ($worksheet->save()) {

            // Packing
            $fields_packing = ['tracking', 'country', 'shipper_name', 'shipper_address', 'shipper_phone', 'shipper_id', 'consignee_name', 'consignee_address', 'consignee_phone', 'consignee_id', 'length', 'width', 'height', 'weight', 'items', 'shipment_val', 'work_sheet_id'];
            $packing = new PackingEng;
            foreach($fields_packing as $field){
                if ($field === 'country') {
                    $packing->$field = $request->input('consignee_country');
                }
                elseif ($field === 'shipper_name') {
                    $packing->$field = $request->input('first_name').' '.$request->input('last_name');
                }
                elseif ($field === 'shipper_phone') {
                    $packing->$field = $request->input('standard_phone');
                }
                elseif ($field === 'consignee_name') {
                    $packing->$field = $request->input('consignee_first_name').' '.$request->input('consignee_last_name');
                }
                elseif ($field === 'work_sheet_id') {
                    $packing->$field = $work_sheet_id;
                }
                else if ($field === 'items') {
                    $temp = '';
                    for ($i=1; $i < 11; $i++) { 
                        if (null !== $request->input('item_'.$i)) {
                            $temp .= $request->input('item_'.$i).' - '.$request->input('q_item_'.$i).'; ';
                        }
                    }
                    $packing->$field = $temp;
                }
                else{
                    $packing->$field = $request->input($field);
                } 
            }
            $packing->save();

            $message = 'Shipment order successfully created !';
        }
        else{
            $message = 'Saving error !';
        }
               
        return redirect()->route('philIndParcelForm')->with('status', $message);        
    }


    public function checkPhone(Request $request)
    {
        $data = NewWorksheet::where([
            ['sender_phone',$request->input('sender_phone')],
            ['site_name', '=', 'DD-C']
        ])
        ->orWhere([
            ['standard_phone',$request->input('sender_phone')],
            ['site_name', '=', 'DD-C']
        ])
        ->get()->last();
        $message = 'Данный номер телефона в системе отсутствует';
        $add_parcel = 'true';
        $data_parcel = [];

        if ($data) {
            if ($request->input('quantity_sender') === '1') {               
                $sender_name = explode(" ", $data->sender_name);
                if ($sender_name) {
                    $data_parcel['first_name'] = $sender_name[0];
                    $data_parcel['last_name'] = $sender_name[1];
                }
                else{
                    $data_parcel['first_name'] = '';
                    $data_parcel['last_name'] = '';
                }               
                $data_parcel['sender_address'] = $data->sender_address;
                $data_parcel['sender_city'] = $data->sender_city;
                $data_parcel['sender_postcode'] = $data->sender_postcode;
                $data_parcel['sender_country'] = $data->sender_country;
                $data_parcel['standard_phone'] = $data->standard_phone;
                $data_parcel['sender_phone'] = $data->sender_phone;
                $data_parcel['sender_passport'] = $data->sender_passport;
            }
            if ($request->input('quantity_recipient') === '1') {
                $recipient_data = PackingSea::where('phone',$data->recipient_phone)->get()->last();
                $recipient_name = explode(" ", $data->recipient_name);
                $data_parcel['recipient_first_name'] = $recipient_name[0];
                $data_parcel['recipient_last_name'] = $recipient_name[1];
                $data_parcel['recipient_street'] = $data->recipient_street;
                $data_parcel['recipient_house'] = $data->recipient_house;
                $data_parcel['recipient_room'] = $data->recipient_room;                
                $data_parcel['recipient_city'] = $data->recipient_city;
                $data_parcel['recipient_postcode'] = $data->recipient_postcode;
                $data_parcel['recipient_country'] = $data->recipient_country;
                $data_parcel['recipient_email'] = $data->recipient_email;
                $data_parcel['recipient_phone'] = $data->recipient_phone;
                $data_parcel['recipient_passport'] = $data->recipient_passport;
                if ($recipient_data) {
                    $data_parcel['body'] = $recipient_data->body;
                    $data_parcel['district'] = $recipient_data->district;
                    $data_parcel['region'] = $recipient_data->region;
                }
            }
            return redirect()->route('parcelForm', ['data_parcel' => $data_parcel])->with('add_parcel', $add_parcel)->with('data_parcel', json_encode($data_parcel));
        }
        else{
            return redirect()->route('parcelForm')->with('no_phone', $message);
        }        
    }


    public function forwardCheckPhone(Request $request)
    {       
        if ($request->input('url_name')) {

            $data = NewWorksheet::where([
                ['sender_phone',$request->input('sender_phone')],
                ['site_name', '=', 'For']
            ])
            ->orWhere([
                ['standard_phone', 'like', '%'.$request->input('sender_phone').'%'],
                ['site_name', '=', 'For']
            ])
            ->get()->last();
            $message = 'Данный номер телефона в системе отсутствует';
            $data_parcel = '?';

            //dd($data);

            if ($data) {
                if ($request->input('quantity_sender') === '1') {
                    $sender_name = explode(" ", $data->sender_name);
                    if ($sender_name) {
                        $data_parcel .= 'first_name='. $sender_name[0].'&';
                        $data_parcel .= 'last_name='. $sender_name[1].'&';
                    }
                    else{
                        $data_parcel .= 'first_name=&';
                        $data_parcel .= 'last_name=&';
                    }
                    $data_parcel .= 'sender_address='. $data->sender_address.'&';
                    $data_parcel .= 'sender_city='. $data->sender_city.'&';
                    $data_parcel .= 'sender_postcode='. $data->sender_postcode.'&';
                    $data_parcel .= 'sender_country='. $data->sender_country.'&';
                    $data_parcel .= 'standard_phone=%2B'. ltrim($data->standard_phone, " \+").'&';
                    $data_parcel .= 'sender_phone='. $data->sender_phone.'&';
                    $data_parcel .= 'sender_passport='.  $data->sender_passport.'&';
                }
                if ($request->input('quantity_recipient') === '1') {
                    $recipient_data = PackingSea::where('phone',$data->recipient_phone)->get()->last();
                    $recipient_name = explode(" ", $data->recipient_name);
                    $data_parcel .= 'recipient_first_name='. $recipient_name[0].'&';
                    $data_parcel .= 'recipient_last_name='. $recipient_name[1].'&';
                    $data_parcel .= 'recipient_street='.  $data->recipient_street.'&';
                    $data_parcel .= 'recipient_house='. $data->recipient_house.'&';
                    $data_parcel .= 'recipient_room='.  $data->recipient_room.'&';                
                    $data_parcel .= 'recipient_city='.  $data->recipient_city.'&';
                    $data_parcel .= 'recipient_postcode='. $data->recipient_postcode.'&';
                    $data_parcel .= 'recipient_country='. $data->recipient_country.'&';
                    $data_parcel .= 'recipient_email='.  $data->recipient_email.'&';
                    $data_parcel .= 'recipient_phone='. $data->recipient_phone.'&';
                    $data_parcel .= 'recipient_passport='.  $data->recipient_passport.'&';
                    if ($recipient_data) {
                        $data_parcel .= 'body='. $recipient_data->body.'&';
                        $data_parcel .= 'district='. $recipient_data->district.'&';
                        $data_parcel .= 'region='.  $recipient_data->region;
                    }
                }
                return redirect($request->input('url_name').$data_parcel);
            }
            else{
                return redirect($request->input('url_name').'?err_message='.$message);
            }  
        }
    }


    public function philIndCheckPhone(Request $request)
    {
        $data = PhilIndWorksheet::where('shipper_phone',$request->input('shipper_phone'))
        ->orWhere('standard_phone',$request->input('shipper_phone'))
        ->get()->last();
        $message = 'This phone number is not available in the system';
        $add_parcel = 'true';
        $data_parcel = [];

        if ($data) {
            if ($request->input('quantity_sender') === '1') {
                $shipper_name = explode(" ", $data->shipper_name);
                if ($shipper_name) {
                    $data_parcel['first_name'] = $shipper_name[0];
                    $data_parcel['last_name'] = $shipper_name[1];
                }
                else{
                    $data_parcel['first_name'] = '';
                    $data_parcel['last_name'] = '';
                }
                $data_parcel['shipper_address'] = $data->shipper_address;
                $data_parcel['standard_phone'] = $data->standard_phone;
                $data_parcel['shipper_phone'] = $data->shipper_phone;
                $data_parcel['shipper_id'] = $data->shipper_id;
            }
            if ($request->input('quantity_recipient') === '1') {
                $consignee_name = explode(" ", $data->consignee_name);
                $data_parcel['consignee_first_name'] = $consignee_name[0];
                $data_parcel['consignee_last_name'] = $consignee_name[1];
                $data_parcel['consignee_address'] = $data->consignee_address;
                $data_parcel['consignee_phone'] = $data->consignee_phone;
                $data_parcel['consignee_id'] = $data->consignee_id;
            }
            return redirect()->route('philIndParcelForm', ['data_parcel' => $data_parcel])->with('add_parcel', $add_parcel)->with('data_parcel', json_encode($data_parcel));
        }
        else{
            return redirect()->route('philIndParcelForm')->with('no_phone', $message);
        }        
    }
}