<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\PhilIndWorksheet;
use Validator;


class PhilIndWorksheetController extends BaseController
{
    // Worksheet status
    private $en_arr = ["Forwarding to the warehouse in the sender country", "At the warehouse in the sender country", "At the customs in the sender country", "Forwarding to the receiver country", "At the customs in the receiver country", "Forwarding to the receiver", "Delivered"];
    private $ru_arr = [
        "Forwarding to the warehouse in the sender country" => "Доставляется на склад в стране отправителя",
        "At the warehouse in the sender country" => "На складе в стране отправителя",
        "At the customs in the sender country" => "На таможне в стране отправителя",
        "Forwarding to the receiver country" => "Доставляется в страну получателя",
        "At the customs in the receiver country" => "На таможне в стране получателя",
        "Forwarding to the receiver" => "Доставляется получателю",
        "Delivered" => "Доставлено"
    ];
    private $he_arr = [
        "Forwarding to the warehouse in the sender country" => "נשלח למחסן במדינת השולח",
        "At the warehouse in the sender country" => "במחסן במדינת השולח",
        "At the customs in the sender country" => " במכס במדינת השולח",
        "Forwarding to the receiver country" => " נשלח למדינת המקבל",
        "At the customs in the receiver country" => " במכס במדינת המקבל",
        "Forwarding to the receiver" => " נמסר למקבל",
        "Delivered" => " נמסר"
    ];
    private $message_arr = ['ru' => '', 'en' => '', 'he' => ''];


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

            $row = PhilIndWorksheet::select('status','status_ru','status_he')
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
                        $this->message_arr['ru'] = $val->status_ru;
                    }
                    if ($val->status) {
                        $this->message_arr['en'] = $val->status;
                    }
                    if ($val->status) {
                        $this->message_arr['he'] = $val->status_he;
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
     * Display the specified resource for clients
     *
     * @param  string  $tracking
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getStatusEng(Request $request, $tracking)
    {    
        if ($this->checkToken($request->token) && $request->token) {

            $row = PhilIndWorksheet::select('status','status_ru','status_he')
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
                        $this->message_arr['ru'] = $val->status_ru;
                    }
                    if ($val->status) {
                        $this->message_arr['en'] = $val->status;
                    }
                    if ($val->status) {
                        $this->message_arr['he'] = $val->status_he;
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
                $row = PhilIndWorksheet::where([
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
                    $sheet = PhilIndWorksheet::find($row[0]->id);
                    foreach ($row as $val) {
                        if ($val->status) {                      
                            $key_num = array_search($val->status, $this->en_arr);
                            if ((int)$key_num < 6) {
                                $en_status = $this->en_arr[(int)$key_num+1];
                                $sheet->status = $en_status;
                                $this->message_arr['en'] = $en_status;
                                $sheet->status_ru = $this->ru_arr[$en_status];
                                $this->message_arr['ru'] = $this->ru_arr[$en_status];
                                $sheet->status_he = $this->he_arr[$en_status];
                                $this->message_arr['he'] = $this->he_arr[$en_status];
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
                $row = PhilIndWorksheet::where([
                    ['tracking_local', '=', $tracking]
                ])
                ->get();

                if ($row->count()) {
                    $sheet = PhilIndWorksheet::find($row[0]->id);
                    if (!$sheet->tracking_main) {
                        $sheet->tracking_main = $input['tracking_main'];
                        $sheet->status = $this->en_arr[0];
                        $sheet->status_ru = $this->ru_arr[$this->en_arr[0]];
                        $sheet->status_he = $this->he_arr[$this->en_arr[0]];
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
                    $sheet = new PhilIndWorksheet;
                    $sheet->tracking_main = $input['tracking_main'];
                    $sheet->tracking_local = $tracking;
                    $sheet->status = $this->en_arr[0];
                    $sheet->status_ru = $this->ru_arr[$this->en_arr[0]];
                    $sheet->status_he = $this->he_arr[$this->en_arr[0]];
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
     * Adding tracking number by phone number.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addTrackingByPhoneEng(Request $request)
    {    
        if ($this->checkToken($request->token) && $request->token){    

            $input = $request->all();
            $validator = Validator::make($input, [
                'tracking_main' => 'required',
                'standard_phone' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }

            $standard_phone = ltrim(urldecode($input['standard_phone']), " \+");

            $this_tracking = PhilIndWorksheet::where([
                ['standard_phone', '+'.$standard_phone],
                ['tracking_main', $input['tracking_main']]
            ])->get();

            if ($this_tracking->count()) {
                return $this->sendError('This tracking number is exist!');
            }           

            $data = PhilIndWorksheet::where('standard_phone', '+'.$standard_phone)->get();

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

                            $i = (int)(PhilIndWorksheet::where([
                                ['standard_phone', '+'.$standard_phone],
                                ['order_number', '<>', null]
                            ])->get()->last()->order_number);

                            $i++;
                            return $item->update(['order_number'=> $i]);
                        }               
                    });
                }
            }           

            $empty_tracking = PhilIndWorksheet::where([
                ['standard_phone', '+'.$standard_phone],
                ['tracking_main', null]
            ])->get();

            $number_of_empty = $empty_tracking->count();

            if ($number_of_empty > 1) {
                return $this->sendResponse($empty_tracking->toArray(), 'Found multiple orders.');
            }
            else if($number_of_empty == 1){
                
                $empty_tracking->last()->update(['tracking_main'=> $input['tracking_main']]);
                $empty_tracking->last()->update(['status'=> $this->en_arr[0]]);
                $empty_tracking->last()->update(['status_ru'=> $this->ru_arr[$this->en_arr[0]]]);
                $empty_tracking->last()->update(['status_he'=> $this->he_arr[$this->en_arr[0]]]);
                
                return $this->sendResponse($empty_tracking->last()->toArray(), 'Row updated successfully.');
            }
            else{
                $data = PhilIndWorksheet::where('standard_phone', '+'.$standard_phone)->get()->last();

                $new_worksheet = new PhilIndWorksheet();
                $new_worksheet->date = date('Y.m.d');
                $new_worksheet->status = $this->en_arr[0];
                $new_worksheet->tracking_main = $input['tracking_main'];
                $new_worksheet->shipper_name = $data->shipper_name;
                $new_worksheet->shipper_address = $data->shipper_address;
                $new_worksheet->standard_phone = $input['standard_phone'];
                $new_worksheet->shipper_id = $data->shipper_id;
                $new_worksheet->order_number = (int)$data->order_number + 1;
                $new_worksheet->status_ru = $this->ru_arr[$this->en_arr[0]];
                $new_worksheet->status_he = $this->he_arr[$this->en_arr[0]];
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
    public function addTrackingByPhoneWithOrderEng(Request $request)
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

            $data = PhilIndWorksheet::where([
                ['standard_phone', '+'.$standard_phone],
                ['order_number', $input['order_number']]
            ])->get()->first();

            $data->update(['tracking_main'=> $input['tracking_main']]);
            $data->update(['status'=> $this->en_arr[0]]);
            $data->update(['status_ru'=> $this->ru_arr[$this->en_arr[0]]]);
            $data->update(['status_he'=> $this->he_arr[$this->en_arr[0]]]);

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
    public function addNewShipmentEng(Request $request)
    {
        if ($this->checkToken($request->token) && $request->token){    

            $input = $request->all();
            $validator = Validator::make($input, [
                'standard_phone' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }

            $standard_phone = ltrim(urldecode($input['standard_phone']), " \+");

            $data = PhilIndWorksheet::where([
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

                            $i = (int)(PhilIndWorksheet::where([
                                ['standard_phone', '+'.$standard_phone],
                                ['order_number', '<>', null]
                            ])->get()->last()->order_number);

                            $i++;
                            return $item->update(['order_number'=> $i]);
                        }               
                    });
                }

                $last = PhilIndWorksheet::where([
                    ['standard_phone', '+'.$standard_phone],
                    ['order_number', '<>', null]
                ])->get()->last();

                $new_worksheet = new PhilIndWorksheet();
                $new_worksheet->date = date('Y.m.d');
                $new_worksheet->status = 'Pick up';
                $new_worksheet->shipper_name = $last->shipper_name;
                $new_worksheet->shipper_address = $last->shipper_address;
                $new_worksheet->standard_phone = $input['standard_phone'];
                $new_worksheet->shipper_id = $last->shipper_id;
                $new_worksheet->order_number = (int)($last->order_number + 1);
                $new_worksheet->save();

                return $this->sendResponse($new_worksheet->toArray(), 'Row created successfully.');
            } 
            else{
                $new_worksheet = new PhilIndWorksheet();
                $new_worksheet->date = date('Y.m.d');
                $new_worksheet->status = 'Pick up';
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
    public function addBatchNumberEng(Request $request)
    {               
        if ($this->checkToken($request->token) && $request->token) {

            $input = $request->all();
            $validator = Validator::make($input, [
                'tracking_main' => 'required',
                'batch_number' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }

            $tracking = $input['tracking_main'];
            
            $row = PhilIndWorksheet::where([
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
                $sheet = PhilIndWorksheet::find($row[0]->id);
                foreach ($row as $val) {
                    if ($val->status) {                      
                        $en_status = $this->en_arr[3];
                        $sheet->status = $en_status;
                        $sheet->status_ru = $this->ru_arr[$en_status];
                        $sheet->status_he = $this->he_arr[$en_status];
                        $sheet->lot = $input['batch_number'];
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
    public function addPalletNumberEng(Request $request)
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

            $row = PhilIndWorksheet::where([
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
                $sheet = PhilIndWorksheet::find($row[0]->id);
                foreach ($row as $val) {
                    if ($val->status) {                      
                        $en_status = $this->en_arr[1];
                        $sheet->status = $en_status;
                        $sheet->status_ru = $this->ru_arr[$en_status];
                        $sheet->status_he = $this->he_arr[$en_status];
                        $sheet->pallet_number = $input['pallet_number'];
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
        
    }
}