<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;
use App\CourierDraftWorksheet;
use App\NewWorksheet;
use App\NewPacking;
use App\PackingSea;
use App\Invoice;
use App\Manifest;
use Auth;
use \Dejurin\GoogleTranslateForFree;
use Excel;
use App\Exports\CourierDraftWorksheetExport;
use App\ReceiptArchive;


class CourierDraftController extends AdminController
{
    public function index(){
        $title = 'Черновик курьеров';
        $courier_draft_worksheet_obj = CourierDraftWorksheet::paginate(10);     
        $user = Auth::user();
        $viewer_arr = parent::VIEWER_ARR;
        
        return view('admin.courier_draft.courier_draft_worksheet', ['title' => $title,'courier_draft_worksheet_obj' => $courier_draft_worksheet_obj, 'user' => $user, 'viewer_arr' => $viewer_arr]);
    }


	public function show($id)
	{
		$courier_draft_worksheet = CourierDraftWorksheet::find($id);
		$title = 'Изменение строки '.$courier_draft_worksheet->id;

		return view('admin.courier_draft.courier_draft_worksheet_update', ['title' => $title,'courier_draft_worksheet' => $courier_draft_worksheet]);
	}


	public function update(Request $request, $id)
	{
		$courier_draft_worksheet = CourierDraftWorksheet::find($id);
		$old_tracking = $courier_draft_worksheet->tracking_main;
		$check_result = '';
		$fields = $this->getTableColumns('courier_draft_worksheet');
		$user = Auth::user();

		foreach($fields as $field){						
			if ($field !== 'created_at' && $field !== 'tracking_main' && stripos($field,'status') === false) {
				$courier_draft_worksheet->$field = $request->input($field);
			}
			elseif ($field !== 'created_at' && ($user->role === 'admin' || $user->role === 'office_1')){
				$courier_draft_worksheet->$field = $request->input($field);
			}
		}

		if ($old_tracking && $request->input('tracking_main')) {
			ReceiptArchive::where('tracking_main', $old_tracking)->delete();
		}
		$notification = ReceiptArchive::where('tracking_main', $request->input('tracking_main'))->first();
		if (!$notification) $check_result = $this->checkReceipt($id, null, 'ru', $request->input('tracking_main'));

		$temp = rtrim($request->input('package_content'), ";");
		$content_arr = explode(";",$temp);

		if ($content_arr[0]) {
			
			// Update Packing Sea
			PackingSea::where('work_sheet_id', $id)
			->update([
				'track_code' => $request->input('tracking_main'),
				'type' => $request->input('tariff'),
				'full_shipper' => $request->input('sender_name'),
				'full_consignee' => $request->input('recipient_name'),
				'country_code' => $request->input('recipient_country'),
				'region' => $request->input('region'),
				'district' => $request->input('district'),
				'postcode' => $request->input('recipient_postcode'),
				'city' => $request->input('recipient_city'),
				'street' => $request->input('recipient_street'),
				'house' => $request->input('recipient_house'),
				'body' => $request->input('body'),
				'room' => $request->input('recipient_room'),
				'phone' => $request->input('recipient_phone')
			]);

			if ($request->input('package_content')) {
				
				$old_packing = PackingSea::where('work_sheet_id', $id)->get();
				$qty = 1;

				for ($i=0; $i < count($content_arr); $i++) { 
					$qty = $i+1;
					$content = explode(':', $content_arr[$i]);

					if (count($content) == 2) {
						if ($qty <= count($old_packing)) {
							PackingSea::where([
								['work_sheet_id', $id],
								['attachment_number', $qty]
							])
							->update([
								'attachment_name' => trim($content[0]),
								'amount_3' => trim($content[1])
							]);
						}
						else{
							$new_packing = new PackingSea();
							$new_packing->work_sheet_id = $id;
							$new_packing->track_code = $request->input('tracking_main');
							$new_packing->type = $request->input('tariff');
							$new_packing->full_shipper = $request->input('sender_name');
							$new_packing->full_consignee = $request->input('recipient_name');
							$new_packing->country_code = $request->input('recipient_country');
							$new_packing->postcode = $request->input('recipient_postcode');
							$new_packing->region = $request->input('region');
							$new_packing->district = $request->input('district');
							$new_packing->city = $request->input('recipient_city');
							$new_packing->street = $request->input('recipient_street');
							$new_packing->house = $request->input('recipient_house');
							$new_packing->body = $request->input('body');
							$new_packing->room = $request->input('recipient_room');
							$new_packing->phone = $request->input('recipient_phone');
							$new_packing->attachment_number = $qty;
							$new_packing->attachment_name = trim($content[0]);
							$new_packing->amount_3 = trim($content[1]);
							$new_packing->save();
						}
					}
					else{
						return redirect()->to(session('this_previous_url'))->with('status-error', 'Ошибка колонки Содержание!'.' '.$check_result);
					}
				}
				PackingSea::where([
					['work_sheet_id', $id],
					['attachment_number','>',$qty]
				])->delete();
			}
			else{
				PackingSea::where('work_sheet_id', $id)->delete();
			}
			// End Update Packing Sea
			$courier_draft_worksheet->save();
			return redirect()->to(session('this_previous_url'))->with('status', 'Строка успешно обновлена!'.' '.$check_result);
		}	
		else{
			return redirect('/admin/courier-draft-worksheet')->with('status-error', 'Ошибка колонки Содержание!'.' '.$check_result);
		}		
	}


	public function destroy(Request $request)
	{
		$id = $request->input('action');

		CourierDraftWorksheet::where('id', $id)->delete();
		PackingSea::where('work_sheet_id', $id)->delete();
		ReceiptArchive::where('worksheet_id', $id)->delete();

		return redirect()->to(session('this_previous_url'))->with('status', 'Строка успешно удалена!');
	}


    public function addCourierDraftDataById(Request $request){
    	$row_arr = $request->input('row_id');
    	$value_by = $request->input('value-by-tracking');
    	$column = $request->input('tracking-columns');
    	$user = Auth::user();

    	if ($row_arr) {
    		if ($value_by && $column) {
    			CourierDraftWorksheet::whereIn('id', $row_arr)
    			->update([
    				$column => $value_by
    			]);       	
    		}
    		else if ($request->input('status') && ($user->role === 'admin' || $user->role === 'office_1')){
    			CourierDraftWorksheet::whereIn('id', $row_arr)
    			->update([
    				'status' => $request->input('status'), 
    				'status_en' => $request->input('status_en'),
    				'status_ua' => $request->input('status_ua'),
    				'status_he' => $request->input('status_he')
    			]);
    		}
    		else if ($request->input('site_name')) {
    			CourierDraftWorksheet::whereIn('id', $row_arr)
    			->update([
    				'site_name' => $request->input('site_name')
    			]);       	
    		}
    		else if ($request->input('tariff')) {
    			CourierDraftWorksheet::whereIn('id', $row_arr)
    			->update([
    				'tariff' => $request->input('tariff')
    			]);       	
    		}
    		else if ($request->input('partner')) {
    			CourierDraftWorksheet::whereIn('id', $row_arr)
    			->update([
    				'partner' => $request->input('partner')
    			]);       	
    		}
    	}
        
        return redirect()->to(session('this_previous_url'))->with('status', 'Строки успешно изменены!');
    }


    public function deleteCourierDraftWorksheetById(Request $request)
	{
		$row_arr = $request->input('row_id');

		CourierDraftWorksheet::whereIn('id', $row_arr)->delete();
		PackingSea::whereIn('work_sheet_id', $row_arr)->delete();
		ReceiptArchive::whereIn('worksheet_id', $row_arr)->delete();

		return redirect()->to(session('this_previous_url'))->with('status', 'Строки успешно удалены!');
	}


	public function courierDraftWorksheetFilter(Request $request){
        $title = 'Фильтр Черновика курьеров';
        $search = $request->table_filter_value;
        $courier_draft_worksheet_arr = [];
        $attributes = CourierDraftWorksheet::first()->attributesToArray();

        $arr_columns = parent::new_columns();

        $user = Auth::user();
        $viewer_arr = parent::VIEWER_ARR; 
        $id_arr = [];
        $new_arr = [];      

        if ($request->table_columns) {
        	$courier_draft_worksheet_obj = CourierDraftWorksheet::where($request->table_columns, 'like', '%'.$search.'%')
        	->paginate(10);
        }
        else{
        	foreach($attributes as $key => $value)
        	{
        		if ($key !== 'created_at' && $key !== 'updated_at' && $key !== 'update_status_date') {
        			$sheet = CourierDraftWorksheet::where($key, 'like', '%'.$search.'%')->get()->first();
        			if ($sheet) {       				
        				$temp_arr = CourierDraftWorksheet::where($key, 'like', '%'.$search.'%')->get();
        				$new_arr = $temp_arr->filter(function ($item, $k) use($id_arr) {
        					if (!in_array($item->id, $id_arr)) { 
        						$id_arr[] = $item->id;       						  
        						return $item;    					
        					}       					       					
        				});        				
        				$courier_draft_worksheet_arr[] = $new_arr;   				         		
        			}
        		}         		
        	}

        	return view('admin.courier_draft.courier_draft_worksheet_find', ['title' => $title,'courier_draft_worksheet_arr' => $courier_draft_worksheet_arr, 'user' => $user, 'viewer_arr' => $viewer_arr]);      	
        }
        
        $data = $request->all();             
        
        return view('admin.courier_draft.courier_draft_worksheet', ['title' => $title,'data' => $data,'courier_draft_worksheet_obj' => $courier_draft_worksheet_obj, 'user' => $user, 'viewer_arr' => $viewer_arr]);
    }


    public function courierDraftCheckActivate($id)
    {
    	$courier_draft_worksheet = CourierDraftWorksheet::find($id);
		$error_message = 'Заполните обязателные поля: ';

		if (!$courier_draft_worksheet->sender_name) $error_message .= 'Отправитель,';
		if (!$courier_draft_worksheet->standard_phone) $error_message .= 'Телефон (стандарт),';
		if (!$courier_draft_worksheet->recipient_name) $error_message .= 'Получатель,';
		if (!$courier_draft_worksheet->recipient_city) $error_message .= 'Город получателя,';
		if (!$courier_draft_worksheet->recipient_street) $error_message .= 'Улица получателя,';
		if (!$courier_draft_worksheet->recipient_house) $error_message .= '№ дома пол-ля,';
		if (!$courier_draft_worksheet->recipient_room) $error_message .= '№ кв. пол-ля,';
		if (!$courier_draft_worksheet->recipient_phone) $error_message .= 'Телефон получателя,';

		if ($error_message !== 'Заполните обязателные поля: ') {
			return response()->json(['error' => $error_message]);
		}
		else{
			return response()->json(['success' => 'success']);
		}			
    }


    public function courierDraftActivate($id)
	{
		$courier_draft_worksheet = CourierDraftWorksheet::find($id);		
		$new_worksheet = new NewWorksheet();
		$fields = $this->getTableColumns('courier_draft_worksheet');

		$check_tracking	= NewWorksheet::where('tracking_main', $courier_draft_worksheet->tracking_main)->first();
		if ($check_tracking) return redirect()->to(session('this_previous_url'))->with('status-error', 'Трекинг существует!');	

		foreach($fields as $field){
			if ($field !== 'created_at' && $field !== 'id') {
				$new_worksheet->$field = $courier_draft_worksheet->$field;
			}			
		}				

		$temp = rtrim($courier_draft_worksheet->package_content, ";");
		$content_arr = explode(";",$temp);
				
		if ($content_arr[0]) {
			
			$new_worksheet->save();
			$work_sheet_id = $new_worksheet->id;

			ReceiptArchive::where('worksheet_id', $id)->update(['worksheet_id' => $work_sheet_id]);
			
			$tr = new GoogleTranslateForFree();
			$packing = PackingSea::where('work_sheet_id', $id)->get();
			
			$this->createNewPacking($new_worksheet, $work_sheet_id, $packing);
			$this->createInvoice($new_worksheet, $tr, $work_sheet_id, $packing);
			$this->createManifest($new_worksheet, $tr, $work_sheet_id, $packing);
			PackingSea::where('work_sheet_id', $id)->delete();

			// Adding order number
            if ($new_worksheet->standard_phone) {

                $standard_phone = ltrim($new_worksheet->standard_phone, " \+");

                $data = NewWorksheet::where('sender_phone', 'like', '%'.$standard_phone.'%')
                ->orWhere('standard_phone', '+'.$standard_phone)
                ->get();

                if (!$data->first()->order_number) {
                    $data->transform(function ($item, $key) {
                        return $item->update(['order_number'=> ((int)$key+1)]);             
                    });
                }
                else{
                    $data->transform(function ($item, $key) use($standard_phone) {
                        if (!$item->order_number) {

                            $i = (int)(NewWorksheet::where([
                                ['sender_phone', 'like', '%'.$standard_phone.'%'],
                                ['order_number', '<>', null]
                            ])
                            ->orWhere([
                                ['standard_phone', '+'.$standard_phone],
                                ['order_number', '<>', null]
                            ])
                            ->get()->last()->order_number);

                            $i++;
                            return $item->update(['order_number'=> $i]);
                        }               
                    });
                }
            }						
			CourierDraftWorksheet::where('id', $id)->delete();
			return redirect()->to(session('this_previous_url'))->with('status', 'Строка успешно активирована!');
		}
		else{
			return redirect()->to(session('this_previous_url'))->with('status-error', 'Ошибка активации!');
		}				
	}


	private function createNewPacking($new_worksheet, $work_sheet_id, $packing){			
		$packing_fields = $this->getTableColumns('new_packing');					

		$packing->each(function ($item, $key) use($work_sheet_id, $packing_fields, $new_worksheet) {
			$new_packing = new NewPacking();
			for ($i=0; $i < count($packing_fields); $i++) { 
				if ($packing_fields[$i] !== 'work_sheet_id' && $packing_fields[$i] !== 'id') {
					$new_packing[$packing_fields[$i]] = $item[$packing_fields[$i]];
				}
				else{
					$new_packing->work_sheet_id = $work_sheet_id;
				}					
			}
			$new_packing->weight_kg = $new_worksheet->weight;
			$new_packing->save();
		});

		return true;
	}


	private function createInvoice($new_worksheet, $tr, $work_sheet_id, $packing){
		$invoice_num = 1;
		$result = Invoice::latest()->first();
		if ($result) {
			$invoice_num = (int)$result->number + 1;
		}			
		$address = '';
		for ($i=0; $i < 8; $i++) { 
			if ($i == 0 && $packing[0]->postcode) {
				$address .= $packing[0]->postcode.', ';
			}
			if ($i == 1 && $packing[0]->region) {
				$address .= $this->translit($packing[0]->region).', ';
			}
			if ($i == 2 && $packing[0]->district) {
				$address .= $this->translit($packing[0]->district).', ';
			}
			if ($i == 3 && $packing[0]->city) {
				$address .= $this->translit($packing[0]->city).', ';
			}
			if ($i == 4 && $packing[0]->street) {
				$address .= $this->translit($packing[0]->street).', ';
			}
			if ($i == 5 && $packing[0]->house) {
				$address .= $packing[0]->house;
			}
			if ($i == 6 && $packing[0]->body) {
				$address .= '/'.$packing[0]->body;
			}
			if ($i == 7 && $packing[0]->room) {
				$address .= ', '.$packing[0]->room;
			}
		}

		$invoice = new Invoice();
		$invoice->number = $invoice_num;
		$invoice->tracking = $new_worksheet->tracking_main;
		$invoice->box = 1;
		$invoice->shipper_name = $this->translit($packing[0]->full_shipper);
		$invoice->shipper_address_phone = $this->translit($new_worksheet->sender_city.', '.$new_worksheet->sender_address).'; '.$new_worksheet->standard_phone;
		$invoice->consignee_name = $this->translit($packing[0]->full_consignee);
		$invoice->consignee_address = $address;
		$invoice->shipped_items = $tr->translate('ru', 'en', $new_worksheet->package_content, 5);
		$invoice->weight = $new_worksheet->weight;
		$invoice->height = $new_worksheet->height;
		$invoice->length = $new_worksheet->length;
		$invoice->width = $new_worksheet->width;
		$invoice->declared_value = $new_worksheet->package_cost;
		$invoice->work_sheet_id = $work_sheet_id;
		$invoice->save();

		return true;
	}


	private function createManifest($new_worksheet, $tr, $work_sheet_id, $packing){
		$manifest_num = 1;
		$result = Manifest::where('number','<>', null)->latest()->first();
		if ($result) {
			$manifest_num = (int)$result->number + 1;
		}

		$address = '';
		for ($i=0; $i < 8; $i++) { 
			if ($i == 0 && $packing[0]->postcode) {
				$address .= $packing[0]->postcode.', ';
			}
			if ($i == 1 && $packing[0]->region) {
				$address .= $this->translit($packing[0]->region).', ';
			}
			if ($i == 2 && $packing[0]->district) {
				$address .= $this->translit($packing[0]->district).', ';
			}
			if ($i == 3 && $packing[0]->city) {
				$address .= $this->translit($packing[0]->city).', ';
			}
			if ($i == 4 && $packing[0]->street) {
				$address .= $this->translit($packing[0]->street).', ';
			}
			if ($i == 5 && $packing[0]->house) {
				$address .= $packing[0]->house;
			}
			if ($i == 6 && $packing[0]->body) {
				$address .= '/'.$packing[0]->body;
			}
			if ($i == 7 && $packing[0]->room) {
				$address .= ', '.$packing[0]->room;
			}
		}

		for ($i=0; $i < count($packing); $i++) { 
			$manifest = new Manifest();
			if ($i == 0) {
				$manifest->number = $manifest_num;
			}
			else{
				$manifest->number = null;
			}
			$manifest->tracking = $new_worksheet->tracking_main;
			$manifest->sender_country = $tr->translate('ru', 'en', $new_worksheet->sender_country, 5);
			$manifest->sender_name = $this->translit($new_worksheet->sender_name);
			$manifest->recipient_name = $this->translit($new_worksheet->recipient_name);
			$manifest->recipient_city = $this->translit($new_worksheet->recipient_city);
			$manifest->recipient_address = $address;
			$manifest->content = $tr->translate('ru', 'en', $packing[$i]->attachment_name, 5);
			$manifest->quantity = $packing[$i]->amount_3;
			$manifest->weight = $new_worksheet->weight;
			$manifest->cost = $new_worksheet->package_cost;
			$manifest->attachment_number = $packing[$i]->attachment_number;
			$manifest->work_sheet_id = $work_sheet_id;
			$manifest->save();
		}

		return true;
	}


	public function exportExcel()
	{

		return Excel::download(new CourierDraftWorksheetExport, 'CourierDraftWorksheetExport.xlsx');

	}

}
