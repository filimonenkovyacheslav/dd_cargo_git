<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;
use App\EngDraftWorksheet;
use App\PhilIndWorksheet;
use Auth;


class EngDraftWorksheetController extends AdminController
{
    public function index(){
        $title = 'Draft';
        $eng_draft_worksheet_obj = EngDraftWorksheet::paginate(10);     
        $user = Auth::user();
        $viewer_arr = parent::VIEWER_ARR;
        
        return view('admin.draft.eng_draft_worksheet', ['title' => $title,'eng_draft_worksheet_obj' => $eng_draft_worksheet_obj, 'user' => $user, 'viewer_arr' => $viewer_arr]);
    }


    public function showAdd()
	{
		$title = 'Add row';
		return view('admin.draft.eng_draft_worksheet_add', ['title' => $title]);
	}


	public function add(Request $request)
	{
		$eng_draft_worksheet = new EngDraftWorksheet();
		$fields = $this->getTableColumns('eng_draft_worksheet');		

		foreach($fields as $field){
			if ($field !== 'created_at') {
				$eng_draft_worksheet->$field = $request->input($field);
			}			
		}

		if (!$eng_draft_worksheet->status) {
			$eng_draft_worksheet->status = 'Pick up';
		}
		
		$eng_draft_worksheet->save();		
		
		return redirect()->to(session('this_previous_url'))->with('status', 'Row added successfully!');
	}


	public function show($id)
	{
		$eng_draft_worksheet = EngDraftWorksheet::find($id);
		$title = 'Update row '.$eng_draft_worksheet->id;

		return view('admin.draft.eng_draft_worksheet_update', ['title' => $title,'eng_draft_worksheet' => $eng_draft_worksheet]);
	}


	public function update(Request $request, $id)
	{
		$eng_draft_worksheet = EngDraftWorksheet::find($id);
		$fields = $this->getTableColumns('eng_draft_worksheet');

		foreach($fields as $field){			
			if ($field !== 'created_at') {
				$eng_draft_worksheet->$field = $request->input($field);
			}
		}
		
		$eng_draft_worksheet->save();	
		
		return redirect()->to(session('this_previous_url'))->with('status', 'Row updated successfully!');
	}


	public function destroy(Request $request)
	{
		$id = $request->input('action');

		EngDraftWorksheet::where('id', $id)
		->delete();

		return redirect()->to(session('this_previous_url'))->with('status', 'Row deleted successfully!');
	}


    public function addEngDraftDataById(Request $request){
    	$row_arr = $request->input('row_id');
    	$value_by = $request->input('value-by-tracking');
    	$column = $request->input('phil-ind-tracking-columns');

    	if ($row_arr) {
    		if ($value_by && $column) {
    			EngDraftWorksheet::whereIn('id', $row_arr)
    			->update([
    				$column => $value_by
    			]);       	
    		}
    		else if ($request->input('status')){
    			EngDraftWorksheet::whereIn('id', $row_arr)
    			->update([
    				'status' => $request->input('status'), 
    				'status_ru' => $request->input('status_ru'),
    				'status_he' => $request->input('status_he')
    			]);
    		}
    	}
        
        return redirect()->to(session('this_previous_url'))->with('status', 'Rows updated successfully!');
    }


    public function deleteEngDraftWorksheetById(Request $request)
	{
		$row_arr = $request->input('row_id');

		EngDraftWorksheet::whereIn('id', $row_arr)
		->delete();

		return redirect()->to(session('this_previous_url'))->with('status', 'Rows deleted successfully!');
	}


	public function engDraftWorksheetFilter(Request $request){
        $title = 'Draft Filter';
        $search = $request->table_filter_value;
        $eng_draft_worksheet_arr = [];
        $attributes = EngDraftWorksheet::first()->attributesToArray();

        $arr_columns = parent::new_columns();

        $user = Auth::user();
        $viewer_arr = parent::VIEWER_ARR; 
        $id_arr = [];
        $new_arr = [];      

        if ($request->table_columns) {
        	$eng_draft_worksheet_obj = EngDraftWorksheet::where($request->table_columns, 'like', '%'.$search.'%')
        	->paginate(10);
        }
        else{
        	foreach($attributes as $key => $value)
        	{
        		if ($key !== 'created_at' && $key !== 'updated_at') {
        			$sheet = EngDraftWorksheet::where($key, 'like', '%'.$search.'%')->get()->first();
        			if ($sheet) {       				
        				$temp_arr = EngDraftWorksheet::where($key, 'like', '%'.$search.'%')->get();
        				$new_arr = $temp_arr->filter(function ($item, $k) use($id_arr) {
        					if (!in_array($item->id, $id_arr)) { 
        						$id_arr[] = $item->id;       						  
        						return $item;    					
        					}       					       					
        				});        				
        				$eng_draft_worksheet_arr[] = $new_arr;   				         		
        			}
        		}         		
        	}

        	return view('admin.draft.eng_draft_worksheet_find', ['title' => $title,'eng_draft_worksheet_arr' => $eng_draft_worksheet_arr, 'user' => $user, 'viewer_arr' => $viewer_arr]);      	
        }
        
        $data = $request->all();             
        
        return view('admin.draft.eng_draft_worksheet_filter', ['title' => $title,'data' => $data,'eng_draft_worksheet_obj' => $eng_draft_worksheet_obj, 'user' => $user, 'viewer_arr' => $viewer_arr]);
    }


    public function engDraftActivate($id)
	{
		$eng_draft_worksheet = EngDraftWorksheet::find($id);		
		$worksheet = new PhilIndWorksheet();
		$fields = $this->getTableColumns('eng_draft_worksheet');		

		foreach($fields as $field){
			if ($field !== 'created_at' && $field !== 'id') {
				$worksheet->$field = $eng_draft_worksheet->$field;
			}			
		}
		
		if ($worksheet->save())	{

            // Adding order number
            if ($worksheet->standard_phone) {

                $standard_phone = ltrim($worksheet->standard_phone, " \+");

                $data = PhilIndWorksheet::where('shipper_phone', 'like', '%'.$standard_phone.'%')
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

                            $i = (int)(PhilIndWorksheet::where([
                                ['shipper_phone', 'like', '%'.$standard_phone.'%'],
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
			
			EngDraftWorksheet::where('id', $id)->delete();
			return redirect()->to(session('this_previous_url'))->with('status', 'Row activated successfully!');
		}
		else{
			return redirect()->to(session('this_previous_url'))->with('status', 'Activate error!');
		}				
	}

}
