<?php

namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use App\CourierDraftWorksheet;
use App\CourierEngDraftWorksheet;
use App\PackingSea;
use App\PackingEng;
use App\NewWorksheet;
use App\PhilIndWorksheet;
use App\DraftWorksheet;
use App\EngDraftWorksheet;
use Validator;
use App\User;
use App\ReceiptArchive;
use DB;


class BaseController extends Controller
{
    protected $token = 'd1k6Lpr2nxEa0R96jCSI5xxUjNkJOLFo2vGllglbqZ1MTHFNunB5b8wfy2pc';
    
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $message)
    {
      $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];
        return response()->json($response, 200);
    }
    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
      $response = [
            'success' => false,
            'message' => $error,
        ];
        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }
        return response()->json($response, $code);
    }


    protected function checkToken($token)
    {
        $row = User::where([
          ['api_token', $token],
          ['role','<>', 'user']
        ])->get();
        if(count($row)){
            return true;
        }
        else{
            return false;
        }
    }


    private function checkPhoneAndAddData($phone, $input, $which_admin)
    {
        $tracking = $input['tracking_main'];
        
        if ($which_admin === 'ru') {
            $site_name = isset($input['site_name'])?(($input['site_name'] === 'DD')?'DD-C':'For'):'For';
            
            $worksheet = NewWorksheet::where([
                ['standard_phone',$phone],
                ['tracking_main',null]
            ])->get();
            $draft = DraftWorksheet::where('standard_phone',$phone)->get();

            if ($worksheet->count() || $draft->count()) {
                if ($worksheet->count()) {
                    // code...
                }
                elseif (!$worksheet->count() && $draft->count()) {
                    
                    CourierDraftWorksheet::create([
                        'tracking_main' => $tracking,
                        'standard_phone' => $phone,
                        'site_name' => $site_name,
                        'package_content' => 'Пусто: 0',
                        'date' => date('Y.m.d'),
                        'status' => 'Доставляется на склад в стране отправителя',
                        'status_en'=> 'Forwarding to the warehouse in the sender country',
                        'status_he'=> "נשלח למחסן במדינת השולח",
                        'status_ua'=> 'Доставляється до складу в країні відправника'
                    ]);

                    $draft = $draft->first();
                    $work_sheet_id = DB::getPdo()->lastInsertId();
                    $courier = CourierDraftWorksheet::find($work_sheet_id);
                    $fields = $this->getTableColumns('draft_worksheet');
                    $full_fields = ['id','update_status_date','updated_at','created_at','tracking_main','standard_phone','site_name','date','status','status_en','status_he','status_ua','parcels_qty'];

                    foreach($fields as $field){                     
                        if (!in_array($field, $full_fields) && $draft->$field) {
                            $courier->$field = $draft->$field;
                        }
                    }
                    $courier->save();

/// Добавить еще в пакинг
                    /*PackingSea::create([
                        'track_code' => $tracking,
                        'work_sheet_id' => $work_sheet_id,
                        'attachment_number' => '1',
                        'attachment_name' => 'Пусто',
                        'amount_3' => '0'
                    ]);*/

                    $notification = ReceiptArchive::where('tracking_main', $tracking)->first();
                    if (!$notification) $this->checkReceipt($work_sheet_id, null, 'ru', $tracking);

                    return true;
                }
                return true;
            }
            else return false;
        }
        elseif ($which_admin === 'en') {
            // code...
        }
        
    }

    
    public function addCourierData(Request $request)
    {
      if ($this->checkToken($request->token) && $request->token) {

            $input = $request->all();
            $validator = Validator::make($input, [
                'tracking_main' => 'required',
                'standard_phone' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }            

            $standard_phone = ltrim(urldecode($input['standard_phone']), " \+");
            $tracking = $input['tracking_main'];
            $site_name = isset($input['site_name'])?(($input['site_name'] === 'DD')?'DD-C':'For'):'For';

            if (!$this->trackingValidate($tracking)) return $this->sendError('Tracking number is not correct.');

            if ((stripos($tracking, 'CD') !== false) || (stripos($tracking, 'BL') !== false)) {
                
                $has_tracking = CourierDraftWorksheet::where('tracking_main',$tracking)->first();
                if ($has_tracking) return $this->sendError('Tracking number exists.');

                /*$added_data = $this->checkPhoneAndAddData('+'.$standard_phone, $input, 'ru');
                if ($added_data) return $this->sendResponse(['tracking_main' => $tracking], 'Post added successfully.');*/
                
                CourierDraftWorksheet::create([
                    'tracking_main' => $tracking,
                    'standard_phone' => '+'.$standard_phone,
                    'site_name' => $site_name,
                    'package_content' => 'Пусто: 0',
                    'date' => date('Y.m.d'),
                    'status' => 'Доставляется на склад в стране отправителя',
                    'status_en'=> 'Forwarding to the warehouse in the sender country',
                    'status_he'=> "נשלח למחסן במדינת השולח",
                    'status_ua'=> 'Доставляється до складу в країні відправника'
                ]);
                $work_sheet_id = DB::getPdo()->lastInsertId();

                PackingSea::create([
                    'track_code' => $tracking,
                    'work_sheet_id' => $work_sheet_id,
                    'attachment_number' => '1',
                    'attachment_name' => 'Пусто',
                    'amount_3' => '0'
                ]);

                $notification = ReceiptArchive::where('tracking_main', $tracking)->first();
                if (!$notification) $this->checkReceipt($work_sheet_id, null, 'ru', $tracking);
            }
            else if ((stripos($tracking, 'IN') !== false) || (stripos($tracking, 'NE') !== false)) {

                $has_tracking = CourierEngDraftWorksheet::where('tracking_main',$tracking)->first();
                if ($has_tracking) return $this->sendError('Tracking number exists.');
                
                CourierEngDraftWorksheet::create([
                    'tracking_main' => $tracking,
                    'standard_phone' => '+'.$standard_phone,
                    'date' => date('Y.m.d'),
                    'shipped_items' => 'Empty: 0',
                    'status' => 'Forwarding to the warehouse in the sender country',
                    'status_ru'=> 'Доставляется на склад в стране отправителя',
                    'status_he'=> "נשלח למחסן במדינת השולח"
                ]);
                $work_sheet_id = DB::getPdo()->lastInsertId();

                PackingEng::create([
                    'tracking' => $tracking,
                    'work_sheet_id' => $work_sheet_id,
                    'shipper_phone' => '+'.$standard_phone
                ]); 

                $notification = ReceiptArchive::where('tracking_main', $tracking)->first();
                if (!$notification) $this->checkReceipt($work_sheet_id, null, 'en', $tracking);
            }
            else{
                return $this->sendError('Tracking number is not correct.');
            }

            return $this->sendResponse(['tracking_main' => $tracking], 'Post added successfully.');
        }
        else{
            return $this->sendError('Token error.');
        }
    }


    public function getShipmentQtyByBatchNumber(Request $request)
    {
        if ($this->checkToken($request->token) && $request->token) {

            $qty = 0;
            $input = $request->all();
            $validator = Validator::make($input, [
                'batch_number' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }

            $batch_number = $input['batch_number'];

            $qty = NewWorksheet::where('batch_number',$batch_number)->count();
            $qty += PhilIndWorksheet::where('lot',$batch_number)->count();

            return $this->sendResponse(['shipment_qty' => $qty], 'Shipment qty retrieved successfully.');
        }
        else{
            return $this->sendError('Token error.');
        }
    }
}