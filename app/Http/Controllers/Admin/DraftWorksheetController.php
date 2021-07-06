<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;
use App\DraftWorksheet;
use App\NewWorksheet;
use Auth;


class DraftWorksheetController extends AdminController
{
    public function index(){
        $title = 'Черновой лист';
        $draft_worksheet_obj = DraftWorksheet::paginate(10);     
        $user = Auth::user();
        $viewer_arr = parent::VIEWER_ARR;
        
        return view('admin.draft.draft_worksheet', ['title' => $title,'draft_worksheet_obj' => $draft_worksheet_obj, 'user' => $user, 'viewer_arr' => $viewer_arr]);
    }


    public function showAdd()
	{
		$title = 'Добавление строки';
		return view('admin.draft.draft_worksheet_add', ['title' => $title]);
	}


	public function add(Request $request)
	{
		$draft_worksheet = new DraftWorksheet();
		$fields = $this->getTableColumns('draft_worksheet');		

		foreach($fields as $field){
			if ($field !== 'created_at') {
				$draft_worksheet->$field = $request->input($field);
			}			
		}

		if (!$draft_worksheet->status) {
			$draft_worksheet->status = 'Забрать';
		}
		
		$draft_worksheet->save();		
		
		return redirect()->to(session('this_previous_url'))->with('status', 'Строка успешно добавлена!');
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

		foreach($fields as $field){			
			if ($field !== 'created_at') {
				$draft_worksheet->$field = $request->input($field);
			}
		}
		
		$draft_worksheet->save();	
		
		return redirect()->to(session('this_previous_url'))->with('status', 'Строка успешно обновлена!');
	}


	public function destroy(Request $request)
	{
		$id = $request->input('action');

		DraftWorksheet::where('id', $id)
		->delete();

		return redirect()->to(session('this_previous_url'))->with('status', 'Строка успешно удалена!');
	}


    public function addDraftDataById(Request $request){
    	$row_arr = $request->input('row_id');
    	$value_by = $request->input('value-by-tracking');
    	$column = $request->input('tracking-columns');

    	if ($row_arr) {
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

		DraftWorksheet::whereIn('id', $row_arr)
		->delete();

		return redirect()->to(session('this_previous_url'))->with('status', 'Строки успешно удалены!');
	}


	public function draftWorksheetFilter(Request $request){
        $title = 'Фильтр Чернового листа';
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
        
        return view('admin.draft.draft_worksheet_filter', ['title' => $title,'data' => $data,'draft_worksheet_obj' => $draft_worksheet_obj, 'user' => $user, 'viewer_arr' => $viewer_arr]);
    }


    public function draftActivate($id)
	{
		$draft_worksheet = DraftWorksheet::find($id);		
		$new_worksheet = new NewWorksheet();
		$fields = $this->getTableColumns('draft_worksheet');		

		foreach($fields as $field){
			if ($field !== 'created_at' && $field !== 'id') {
				$new_worksheet->$field = $draft_worksheet->$field;
			}			
		}
		
		if ($new_worksheet->save())	{

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
			
			DraftWorksheet::where('id', $id)->delete();
			return redirect()->to(session('this_previous_url'))->with('status', 'Строка успешно активирована!');
		}
		else{
			return redirect()->to(session('this_previous_url'))->with('status', 'Ошибка активации!');
		}				
	}

}
