<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;
use App\DraftWorksheet;
use App\NewWorksheet;
use App\NewPacking;
use App\PackingSea;
use App\Invoice;
use App\Manifest;
use Auth;
use \Dejurin\GoogleTranslateForFree;
use Excel;
use App\Exports\DraftWorksheetExport;


class DraftWorksheetController extends AdminController
{
	private $status_arr = ["Доставляется на склад в стране отправителя", "Возврат", "Коробка", "Забрать", "Уточнить", "Думают", "Отмена", "Подготовка", "На складе в стране отправителя"];   

    public function index(){
        $title = 'Черновой лист old';
        $draft_worksheet_obj = DraftWorksheet::paginate(10);     
        $user = Auth::user();
        $viewer_arr = parent::VIEWER_ARR;
        
        return view('admin.draft.draft_worksheet', ['title' => $title,'draft_worksheet_obj' => $draft_worksheet_obj, 'user' => $user, 'viewer_arr' => $viewer_arr]);
    }


	public function show($id)
	{
		$draft_worksheet = DraftWorksheet::find($id);
		$title = 'Изменение строки '.$draft_worksheet->id;

		return view('admin.draft.draft_worksheet_update', ['title' => $title,'draft_worksheet' => $draft_worksheet]);
	}


	public function update(Request $request, $id)
	{
		$draft_worksheet = DraftWorksheet::find($id);
		$fields = $this->getTableColumns('draft_worksheet');
		$error_message = '';

		if ($request->input('recipient_phone')) {
			$error_message = $this->checkConsigneePhone($request->input('recipient_phone'), 'ru');
			if ($error_message) return redirect()->to(session('this_previous_url'))->with('status-error', $error_message);
		}

		foreach($fields as $field){						
			if ($field !== 'created_at') {
				$draft_worksheet->$field = $request->input($field);
			}
		}

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
						return redirect()->to(session('this_previous_url'))->with('status-error', 'Ошибка колонки Содержание!');
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
			$draft_worksheet->save();
			return redirect()->to(session('this_previous_url'))->with('status', 'Строка успешно обновлена!');
		}	
		else{
			return redirect('/admin/draft-worksheet')->with('status-error', 'Ошибка колонки Содержание!');
		}		
	}


	public function destroy(Request $request)
	{
		$id = $request->input('action');

		DraftWorksheet::where('id', $id)->delete();
		PackingSea::where('work_sheet_id', $id)->delete();

		return redirect()->to(session('this_previous_url'))->with('status', 'Строка успешно удалена!');
	}


    public function addDraftDataById(Request $request){
    	$row_arr = $request->input('row_id');
    	$value_by = $request->input('value-by-tracking');
    	$column = $request->input('tracking-columns');

    	if ($row_arr) {
    		
    		if ($column === 'recipient_phone') {
    			$error_message = $this->checkConsigneePhone($value_by, 'ru');
    			if ($error_message) return redirect()->to(session('this_previous_url'))->with('status-error', $error_message);
    		}    		
    		
    		if ($value_by && $column) {
    			DraftWorksheet::whereIn('id', $row_arr)
    			->update([
    				$column => $value_by
    			]);       	
    		}
    		else if ($request->input('status')){
    			DraftWorksheet::whereIn('id', $row_arr)
    			->update([
    				'status' => $request->input('status'), 
    				'status_en' => $request->input('status_en'),
    				'status_ua' => $request->input('status_ua'),
    				'status_he' => $request->input('status_he')
    			]);
    		}
    		else if ($request->input('site_name')) {
    			DraftWorksheet::whereIn('id', $row_arr)
    			->update([
    				'site_name' => $request->input('site_name')
    			]);       	
    		}
    		else if ($request->input('tariff')) {
    			DraftWorksheet::whereIn('id', $row_arr)
    			->update([
    				'tariff' => $request->input('tariff')
    			]);       	
    		}
    		else if ($request->input('partner')) {
    			DraftWorksheet::whereIn('id', $row_arr)
    			->update([
    				'partner' => $request->input('partner')
    			]);       	
    		}
    	}
        
        return redirect()->to(session('this_previous_url'))->with('status', 'Строки успешно изменены!');
    }


    public function deleteDraftWorksheetById(Request $request)
	{
		$row_arr = $request->input('row_id');

		DraftWorksheet::whereIn('id', $row_arr)->delete();
		PackingSea::whereIn('work_sheet_id', $row_arr)->delete();

		return redirect()->to(session('this_previous_url'))->with('status', 'Строки успешно удалены!');
	}


	public function draftWorksheetFilter(Request $request){
        $title = 'Фильтр Чернового листа old';
        $search = $request->table_filter_value;
        $draft_worksheet_arr = [];
        $attributes = DraftWorksheet::first()->attributesToArray();

        $arr_columns = parent::new_columns();

        $user = Auth::user();
        $viewer_arr = parent::VIEWER_ARR; 
        $id_arr = [];
        $new_arr = [];      

        if ($request->table_columns) {
        	$draft_worksheet_obj = DraftWorksheet::where($request->table_columns, 'like', '%'.$search.'%')
        	->paginate(10);
        }
        else{
        	foreach($attributes as $key => $value)
        	{
        		if ($key !== 'created_at' && $key !== 'updated_at' && $key !== 'update_status_date') {
        			$sheet = DraftWorksheet::where($key, 'like', '%'.$search.'%')->get()->first();
        			if ($sheet) {       				
        				$temp_arr = DraftWorksheet::where($key, 'like', '%'.$search.'%')->get();
        				$new_arr = $temp_arr->filter(function ($item, $k) use($id_arr) {
        					if (!in_array($item->id, $id_arr)) { 
        						$id_arr[] = $item->id;       						  
        						return $item;    					
        					}       					       					
        				});        				
        				$draft_worksheet_arr[] = $new_arr;   				         		
        			}
        		}         		
        	}

        	return view('admin.draft.draft_worksheet_find', ['title' => $title,'draft_worksheet_arr' => $draft_worksheet_arr, 'user' => $user, 'viewer_arr' => $viewer_arr]);      	
        }
        
        $data = $request->all();             
        
        return view('admin.draft.draft_worksheet', ['title' => $title,'data' => $data,'draft_worksheet_obj' => $draft_worksheet_obj, 'user' => $user, 'viewer_arr' => $viewer_arr]);
    }


    public function draftCheckActivate($id)
    {   	
    	$draft_worksheet = DraftWorksheet::find($id);
		$error_message = 'Заполните обязателные поля: ';
		$user = Auth::user();
		$error_arr = ['error' => '', 'status_error' => ''];

		if (!$draft_worksheet->sender_name) $error_message .= 'Отправитель,';
		if (!$draft_worksheet->standard_phone) $error_message .= 'Телефон (стандарт),';
		if (!$draft_worksheet->recipient_name) $error_message .= 'Получатель,';
		if (!$draft_worksheet->recipient_city) $error_message .= 'Город получателя,';
		if (!$draft_worksheet->recipient_street) $error_message .= 'Улица получателя,';
		if (!$draft_worksheet->recipient_house) $error_message .= '№ дома пол-ля,';
		if (!$draft_worksheet->recipient_room) $error_message .= '№ кв. пол-ля,';
		if (!$draft_worksheet->recipient_phone) $error_message .= 'Телефон получателя,';
		$error_arr['error'] = $error_message;		

		if ($draft_worksheet->status !== 'Забрать') {
			$error_arr['status_error'] = 'Статус должен быть - Забрать';
		}

		if ($error_arr['error'] === 'Заполните обязателные поля: ') {
			$error_arr['error'] = '';
		}

		if ($error_arr) {
			return response()->json($error_arr);
		}	

		$phone_exist = NewWorksheet::where('standard_phone',$draft_worksheet->standard_phone)->get()->last();

		if ($phone_exist) {
			if (in_array($phone_exist->status, $this->status_arr)) {
				return response()->json(['phone_exist' => $id]);
			}
			else{
				return response()->json(['phone_exist' => '']);
			}
		}
		else{
			return response()->json(['phone_exist' => '']);
		}		
    }


    public function draftActivate($id)
    {
    	$result = $this->mainDraftActivate($id);
    	if ($result['status']) {
    		return redirect()->to(session('this_previous_url'))->with('status', $result['status']);
    	}
    	elseif ($result['status_error']) {
    		return redirect()->to(session('this_previous_url'))->with('status-error', $result['status_error']);
    	}
    	else return redirect()->to(session('this_previous_url'))->with('status-error', 'Ошибка активации!');
    }


	public function exportExcel()
	{
		return Excel::download(new DraftWorksheetExport, 'DraftWorksheetExport.xlsx');
	}

}
