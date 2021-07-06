<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Hash;
use App\NewWorksheet;
use Validator;
use Illuminate\Support\Facades\Storage;


class NewWorksheetController extends BaseController
{

    use AuthenticatesUsers;
    
    // Worksheet status
    private $ru_arr = ["Доставляется на склад в стране отправителя", "На складе в стране отправителя", "На таможне в стране отправителя", "Доставляется в страну получателя", "На таможне в стране получателя", "Доставляется получателю", "Доставлено"];
    private $en_arr = [
        "Доставляется на склад в стране отправителя" => "Forwarding to the warehouse in the sender country",
        "На складе в стране отправителя" => "At the warehouse in the sender country",
        "На таможне в стране отправителя" => "At the customs in the sender country",
        "Доставляется в страну получателя" => "Forwarding to the receiver country",
        "На таможне в стране получателя" => "At the customs in the receiver country",
        "Доставляется получателю" => "Forwarding to the receiver",
        "Доставлено" => "Delivered"
    ];
    private $he_arr = [
        "Доставляется на склад в стране отправителя" => "נשלח למחסן במדינת השולח",
        "На складе в стране отправителя" => "במחסן במדינת השולח",
        "На таможне в стране отправителя" => " במכס במדינת השולח",
        "Доставляется в страну получателя" => " נשלח למדינת המקבל",
        "На таможне в стране получателя" => " במכס במדינת המקבל",
        "Доставляется получателю" => " נמסר למקבל",
        "Доставлено" => " נמסר"
    ];
    private $ua_arr = [
        "Доставляется на склад в стране отправителя" => "Доставляється до складу в країні відправника",
        "На складе в стране отправителя" => "На складі в країні відправника",
        "На таможне в стране отправителя" => "На митниці в країні відправника",
        "Доставляется в страну получателя" => "Доставляється в країну отримувача",
        "На таможне в стране получателя" => "На митниці в країні отримувача",
        "Доставляется получателю" => "Доставляється отримувачу",
        "Доставлено" => "Доставлено"
    ];
    private $message_arr = ['ru' => '', 'en' => '', 'he' => '', 'ua' => ''];
    

    /**
     * Display the specified resource.
     *
     * @param  string  $tracking
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $tracking)
    {   
        if ($this->checkToken($request->token) && $request->token) {

            $row = NewWorksheet::select('status','status_en','status_he','status_ua')
            ->where([
                ['tracking_main', '=', $tracking]
            ])
            ->orWhere([
                ['tracking_main', 'like', '%'.', '.$tracking]
            ])
            ->orWhere([
                ['tracking_main', 'like', '%'.$tracking.', '.'%']
            ])
            ->get();

            if ($row->count()) {
                foreach ($row as $val) {
                    if ($val->status) {                    
                        $this->message_arr['ru'] = $val->status;
                    }
                    if ($val->status) {
                        $this->message_arr['en'] = $val->status_en;
                    }
                    if ($val->status) {
                        $this->message_arr['he'] = $val->status_he;
                    }
                    if ($val->status) {
                        $this->message_arr['ua'] = $val->status_ua;
                    }
                }
            }
            else{
                return $this->sendError('Tracking number not found.');
            }

            return $this->sendResponse($this->message_arr, 'Status retrieved successfully.');
        }
        else{
            return $this->sendError('Token error.');
        }
    }


    /**
     * Display the specified resource for clients.
     *
     * @param  string  $tracking
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getStatus(Request $request, $tracking)
    {   
        if ($this->checkToken($request->token) && $request->token) {

            $row = NewWorksheet::select('status','status_en','status_he','status_ua')
            ->where([
                ['tracking_main', '=', $tracking]
            ])
            ->orWhere([
                ['tracking_main', 'like', '%'.', '.$tracking]
            ])
            ->orWhere([
                ['tracking_main', 'like', '%'.$tracking.', '.'%']
            ])
            ->get();

            if ($row->count()) {
                foreach ($row as $val) {
                    if ($val->status) {                    
                        $this->message_arr['ru'] = $val->status;
                    }
                    if ($val->status) {
                        $this->message_arr['en'] = $val->status_en;
                    }
                    if ($val->status) {
                        $this->message_arr['he'] = $val->status_he;
                    }
                    if ($val->status) {
                        $this->message_arr['ua'] = $val->status_ua;
                    }
                }
            }
            else{
                return $this->sendError('Tracking number not found.');
            }

            return $this->sendResponse($this->message_arr, 'Status retrieved successfully.');
        }
        else{
            return $this->sendError('Token error.');
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $tracking
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $tracking)
    {               
        if ($this->checkToken($request->token) && $request->token) {

            $input = $request->all();
            $validator = Validator::make($input, [
                'next_status' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }
            
            if ($input['next_status']) {
                $row = NewWorksheet::where([
                    ['tracking_main', '=', $tracking]
                ])
                ->orWhere([
                    ['tracking_main', 'like', '%'.', '.$tracking]
                ])
                ->orWhere([
                    ['tracking_main', 'like', '%'.$tracking.', '.'%']
                ])
                ->get();

                if ($row->count()) {
                    $sheet = NewWorksheet::find($row[0]->id);
                    foreach ($row as $val) {
                        if ($val->status) {                      
                            $key_num = array_search($val->status, $this->ru_arr);
                            if ((int)$key_num < 6) {
                                $ru_status = $this->ru_arr[(int)$key_num+1];
                                $sheet->status = $ru_status;
                                $this->message_arr['ru'] = $ru_status;
                                $sheet->status_en = $this->en_arr[$ru_status];
                                $this->message_arr['en'] = $this->en_arr[$ru_status];
                                $sheet->status_he = $this->he_arr[$ru_status];
                                $this->message_arr['he'] = $this->he_arr[$ru_status];
                                $sheet->status_ua = $this->ua_arr[$ru_status];
                                $this->message_arr['ua'] = $this->ua_arr[$ru_status];
                                if ($sheet->save()) {
                                    return $this->sendResponse($this->message_arr, 'Status updated successfully.');
                                }
                                else{
                                    return $this->sendError('Saving error.');
                                }
                            }
                            else{
                                return $this->sendError('Status is last.');
                            }
                            break;
                        }
                    }
                }
                else{
                    return $this->sendError('Tracking number not found.');
                }
            } 
        }
        else{
            return $this->sendError('Token error.');
        }                                 
    }
    

    /**
     * Edit the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $tracking
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $tracking)
    {
        if ($this->checkToken($request->token) && $request->token){
            $input = $request->all();
            $validator = Validator::make($input, [
                'tracking_main' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }

            if ($input['tracking_main']) {
                $row = NewWorksheet::where([
                    ['tracking_transit', '=', $tracking]
                ])
                ->get();

                if ($row->count()) {
                    $sheet = NewWorksheet::find($row[0]->id);
                    if (!$sheet->tracking_main) {
                        $sheet->tracking_main = $input['tracking_main'];
                        $sheet->status = $this->ru_arr[0];
                        $sheet->status_en = $this->en_arr[$this->ru_arr[0]];
                        $sheet->status_he = $this->he_arr[$this->ru_arr[0]];
                        $sheet->status_ua = $this->ua_arr[$this->ru_arr[0]];
                    }
                    else{
                        return $this->sendError(['tracking_main' => $sheet->tracking_main], 'Main tracking number already exist.');
                    }                     
                    
                    if ($sheet->save()) {
                        return $this->sendResponse($sheet->toArray(), 'Main tracking number created successfully.');
                    }
                    else{
                        return $this->sendError('Saving error.');
                    }
                }
                else{
                    $sheet = new NewWorksheet;
                    $sheet->tracking_main = $input['tracking_main'];
                    $sheet->tracking_transit = $tracking;
                    $sheet->status = $this->ru_arr[0];
                    $sheet->status_en = $this->en_arr[$this->ru_arr[0]];
                    $sheet->status_he = $this->he_arr[$this->ru_arr[0]];
                    $sheet->status_ua = $this->ua_arr[$this->ru_arr[0]];
                    if ($sheet->save()) {
                        return $this->sendResponse($sheet->toArray(), 'Row created successfully.');
                    }
                    else{
                        return $this->sendError('Saving error.');
                    }
                }
            }
        }
        else{
            return $this->sendError('Token error.');
        }
    }


    /**
     * Login and get token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {        
        $this->validateLogin($request);

        if ($this->attemptLogin($request)) {
            $user = $this->guard()->user();
            $user->api_token = Hash::make(mt_rand(8,15));
            $user->save();
            
            return response()->json([
                'data' => $user->toArray(),
            ]);
        }

        return $this->sendFailedLoginResponse($request);
    }


    /**
     * Adding tracking number by phone number.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addTrackingByPhone(Request $request)
    {    
        if ($this->checkToken($request->token) && $request->token){    

            $input = $request->all();
            $validator = Validator::make($input, [
                'tracking_main' => 'required',
                'standard_phone' => 'required',
                'site_name' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }

            $standard_phone = ltrim(urldecode($input['standard_phone']), " \+");

            $this_tracking = NewWorksheet::where([
                ['standard_phone', '+'.$standard_phone],
                ['tracking_main', $input['tracking_main']]
            ])->get();

            if ($this_tracking->count()) {
                return $this->sendError('This tracking number is exist!');
            }           

            $data = NewWorksheet::where('standard_phone', '+'.$standard_phone)
            ->get();

            if ($data->count()) {
                
                // Adding order number
                if (!$data->first()->order_number) {
                    $data->transform(function ($item, $key) {
                        return $item->update(['order_number'=> ((int)$key+1)]);             
                    });
                }
                else{
                    $data->transform(function ($item, $key) use($standard_phone) {
                        if (!$item->order_number) {

                            $i = (int)(NewWorksheet::where([
                                ['standard_phone', '+'.$standard_phone],
                                ['order_number', '<>', null]
                            ])->get()->last()->order_number);

                            $i++;
                            return $item->update(['order_number'=> $i]);
                        }               
                    });
                }
            }            

            $empty_tracking = NewWorksheet::where([
                ['standard_phone', '+'.$standard_phone],
                ['tracking_main', null]
            ])->get();

            $number_of_empty = $empty_tracking->count();

            if ($number_of_empty > 1) {
                return $this->sendResponse($empty_tracking->toArray(), 'Found multiple orders.');
            }
            else if($number_of_empty == 1){

                $empty_tracking->last()->update(['tracking_main'=> $input['tracking_main']]);
                $empty_tracking->last()->update(['status'=> $this->ru_arr[0]]);
                $empty_tracking->last()->update(['status_en'=> $this->en_arr[$this->ru_arr[0]]]);
                $empty_tracking->last()->update(['status_he'=> $this->he_arr[$this->ru_arr[0]]]);
                $empty_tracking->last()->update(['status_ua'=> $this->ua_arr[$this->ru_arr[0]]]);
                
                return $this->sendResponse($empty_tracking->last()->toArray(), 'Row updated successfully.');
            }
            else{
                $data = NewWorksheet::where('standard_phone', '+'.$standard_phone)->get()->last();

                $new_worksheet = new NewWorksheet();
                $new_worksheet->site_name = $input['site_name'];
                $new_worksheet->date = date('Y.m.d');
                $new_worksheet->status = $this->ru_arr[0];
                $new_worksheet->tracking_main = $input['tracking_main'];
                $new_worksheet->sender_name = $data->sender_name;
                $new_worksheet->sender_country = $data->sender_country;
                $new_worksheet->sender_city = $data->sender_city;
                $new_worksheet->sender_postcode = $data->sender_postcode;
                $new_worksheet->sender_address = $data->sender_address;
                $new_worksheet->standard_phone = $input['standard_phone'];
                $new_worksheet->sender_passport = $data->sender_passport;
                $new_worksheet->order_number = (int)($data->order_number) + 1;
                $new_worksheet->status_en = $this->en_arr[$this->ru_arr[0]];
                $new_worksheet->status_he = $this->he_arr[$this->ru_arr[0]];
                $new_worksheet->status_ua = $this->ua_arr[$this->ru_arr[0]];
                $new_worksheet->save();

                return $this->sendResponse($new_worksheet->toArray(), 'Row created successfully.');
            } 
        }
        else{
            return $this->sendError('Token error.');
        }          
    } 


    /**
     * Adding tracking number by phone number with order number.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addTrackingByPhoneWithOrder(Request $request)
    {
        if ($this->checkToken($request->token) && $request->token){
            $input = $request->all();
            $validator = Validator::make($input, [
                'tracking_main' => 'required',
                'standard_phone' => 'required',
                'order_number' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }

            $standard_phone = ltrim(urldecode($input['standard_phone']), " \+");

            $data = NewWorksheet::where([
                ['standard_phone', '+'.$standard_phone],
                ['order_number', $input['order_number']]
            ])->get()->first();

            $data->update(['tracking_main'=> $input['tracking_main']]);
            $data->update(['status'=> $this->ru_arr[0]]);
            $data->update(['status_en'=> $this->en_arr[$this->ru_arr[0]]]);
            $data->update(['status_he'=> $this->he_arr[$this->ru_arr[0]]]);
            $data->update(['status_ua'=> $this->ua_arr[$this->ru_arr[0]]]);
            
            return $this->sendResponse($data->toArray(), 'Row updated successfully.');
        }
        else{
            return $this->sendError('Token error.');
        } 
    }  


    /**
     * Adding a new shipment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addNewShipment(Request $request)
    {
        if ($this->checkToken($request->token) && $request->token){    

            $input = $request->all();
            $validator = Validator::make($input, [
                'standard_phone' => 'required',
                'site_name' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }

            $standard_phone = ltrim(urldecode($input['standard_phone']), " \+");

            $data = NewWorksheet::where([
                ['standard_phone', '+'.$standard_phone]
            ])->get();

            if ($data->count()) {

                // Adding order number
                if (!$data->first()->order_number) {
                    $data->transform(function ($item, $key) {
                        return $item->update(['order_number'=> ((int)$key+1)]);             
                    });
                }
                else{
                    $data->transform(function ($item, $key) use($standard_phone) {
                        if (!$item->order_number) {

                            $i = (int)(NewWorksheet::where([
                                ['standard_phone', '+'.$standard_phone],
                                ['order_number', '<>', null]
                            ])->get()->last()->order_number);

                            $i++;
                            return $item->update(['order_number'=> $i]);
                        }               
                    });
                }

                $last = NewWorksheet::where([
                    ['standard_phone', '+'.$standard_phone],
                    ['order_number', '<>', null]
                ])->get()->last();

                $new_worksheet = new NewWorksheet();
                $new_worksheet->site_name = $input['site_name'];
                $new_worksheet->date = date('Y.m.d');
                $new_worksheet->status = 'Забрать';
                $new_worksheet->sender_name = $last->sender_name;
                $new_worksheet->sender_country = $last->sender_country;
                $new_worksheet->sender_city = $last->sender_city;
                $new_worksheet->sender_postcode = $last->sender_postcode;
                $new_worksheet->sender_address = $last->sender_address;
                $new_worksheet->standard_phone = $input['standard_phone'];
                $new_worksheet->sender_passport = $last->sender_passport;
                $new_worksheet->order_number = (int)($last->order_number) + 1;
                $new_worksheet->save();

                return $this->sendResponse($new_worksheet->toArray(), 'Row created successfully.');
            } 
            else{
                $new_worksheet = new NewWorksheet();
                $new_worksheet->site_name = $input['site_name'];
                $new_worksheet->date = date('Y.m.d');
                $new_worksheet->status = 'Забрать';
                $new_worksheet->standard_phone = $input['standard_phone'];
                $new_worksheet->order_number = 1;
                $new_worksheet->save();

                return $this->sendResponse($new_worksheet->toArray(), 'Row created successfully.');
            }
        }
        else{
            return $this->sendError('Token error.');
        } 
    }


    /**
     * Adding batch number and updating status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addBatchNumber(Request $request)
    {
        if ($this->checkToken($request->token) && $request->token){
            $input = $request->all();
            $validator = Validator::make($input, [
                'tracking_main' => 'required',
                'batch_number' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }

            $tracking = $input['tracking_main'];

            $row = NewWorksheet::where([
                ['tracking_main', '=', $tracking]
            ])
            ->orWhere([
                ['tracking_main', 'like', '%'.', '.$tracking]
            ])
            ->orWhere([
                ['tracking_main', 'like', '%'.$tracking.', '.'%']
            ])
            ->get();

            if ($row->count()) {
                $sheet = NewWorksheet::find($row[0]->id);
                foreach ($row as $val) {
                    if ($val->status) {                      
                        $ru_status = $this->ru_arr[3];
                        $sheet->status = $ru_status;
                        $sheet->batch_number = $input['batch_number'];
                        $sheet->status_en = $this->en_arr[$ru_status];
                        $sheet->status_he = $this->he_arr[$ru_status];
                        $sheet->status_ua = $this->ua_arr[$ru_status];
                        if ($sheet->save()) {
                            return $this->sendResponse($sheet->toArray(), 'Status updated successfully. Batch number added.');
                        }
                        else{
                            return $this->sendError('Saving error.');
                        }
                        break;
                    }
                }
            }
            else{
                return $this->sendError('Tracking number not found.');
            }
        }
        else{
            return $this->sendError('Token error.');
        } 
    }  


    /**
     * Adding pallet number and updating status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addPalletNumber(Request $request)
    {
        if ($this->checkToken($request->token) && $request->token){
            $input = $request->all();
            $validator = Validator::make($input, [
                'tracking_main' => 'required',
                'pallet_number' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }

            $tracking = $input['tracking_main'];

            $row = NewWorksheet::where([
                ['tracking_main', '=', $tracking]
            ])
            ->orWhere([
                ['tracking_main', 'like', '%'.', '.$tracking]
            ])
            ->orWhere([
                ['tracking_main', 'like', '%'.$tracking.', '.'%']
            ])
            ->get();

            if ($row->count()) {
                $sheet = NewWorksheet::find($row[0]->id);
                foreach ($row as $val) {
                    if ($val->status) {                      
                        $ru_status = $this->ru_arr[1];
                        $sheet->status = $ru_status;
                        $sheet->pallet_number = $input['pallet_number'];
                        $sheet->status_en = $this->en_arr[$ru_status];
                        $sheet->status_he = $this->he_arr[$ru_status];
                        $sheet->status_ua = $this->ua_arr[$ru_status];
                        if ($sheet->save()) {
                            return $this->sendResponse($sheet->toArray(), 'Status updated successfully. Pallet number added.');
                        }
                        else{
                            return $this->sendError('Saving error.');
                        }
                        break;
                    }
                }
            }
            else{
                return $this->sendError('Tracking number not found.');
            }
        }
        else{
            return $this->sendError('Token error.');
        } 
    }  


    /**
     * Store a newly created resource in storage for clients
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /*$new_worksheet = new NewWorksheet();
        $fields = ['site_name', 'date', 'direction', 'tariff', 'status', 'tracking_main', 'tracking_local', 'tracking_transit', 'comment_1', 'comment_2', 'comments', 'sender_name', 'sender_country', 'sender_city', 'sender_postcode', 'sender_address', 'sender_phone', 'sender_passport', 'recipient_name', 'recipient_country', 'recipient_city', 'recipient_postcode', 'recipient_street', 'recipient_house', 'recipient_room', 'recipient_phone', 'recipient_passport', 'recipient_email', 'package_content', 'package_cost', 'courier', 'pick_up_date', 'weight', 'width', 'height', 'length', 'volume_weight', 'quantity_things', 'batch_number', 'pay_date', 'pay_sum', 'status_en', 'status_he', 'status_ua'];        

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
            else if($field === 'comment_2'){
                $new_worksheet->$field = $request->input('need_box');
            }
            else{
                $new_worksheet->$field = $request->input($field);
            }           
        }

        $new_worksheet->save();
        $work_sheet_id = $new_worksheet->id;

        
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

        $message = 'Заказ посылки успешно создан !';
        
        return redirect()->route('parcelForm')->with('status', $message);*/
    }
}