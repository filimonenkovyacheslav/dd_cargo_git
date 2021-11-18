<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;
use App\EngDraftWorksheet;
use App\PhilIndWorksheet;
use App\PackingEng;
use App\PackingEngNew;
use Auth;
use Excel;
use App\Exports\EngDraftWorksheetExport;


class EngDraftWorksheetController extends AdminController
{
	private $status_arr = ["Forwarding to the warehouse in the sender country", "Pending", "Return", "Box", "Pick up", "Specify", "Think", "Canceled", 'At the warehouse in the sender country'];
    

    public function index(){
        $title = 'Draft';
        $eng_draft_worksheet_obj = EngDraftWorksheet::paginate(10);     
        $user = Auth::user();
        $viewer_arr = parent::VIEWER_ARR;
        
        return view('admin.draft.eng_draft_worksheet', ['title' => $title,'eng_draft_worksheet_obj' => $eng_draft_worksheet_obj, 'user' => $user, 'viewer_arr' => $viewer_arr]);
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
		$operator_change = true;
		$user = Auth::user();
		$phone_exist = EngDraftWorksheet::where([
			['standard_phone',$request->input('standard_phone')],
			['id', '<>', $id]
		])->first();
        
        if ($phone_exist) return redirect()->to(session('this_previous_url'))->with('status-error', 'The phone number already exists in Draft!');

		if ($eng_draft_worksheet->operator) $operator_change = false;

		foreach($fields as $field){			
			if ($field !== 'created_at' && $field !== 'operator'){
				$eng_draft_worksheet->$field = $request->input($field);
			}
			elseif ($field === 'operator' && ($user->role === 'admin' || $user->role === 'office_1')) {
				$eng_draft_worksheet->$field = $request->input($field);
			}
			elseif ($field === 'operator' && $user->role !== 'admin' && $operator_change) {
				$eng_draft_worksheet->$field = $request->input($field);
			}
		}
		
		if ($eng_draft_worksheet->save()){
			// Update Update Old Packing Eng
			$address = explode(' ',$request->input('consignee_address'));
			PackingEng::where('work_sheet_id', $id)
			->update([
				'tracking' => $request->input('tracking_main'),
				'country' => $address[0],
				'shipper_name' => $request->input('shipper_name'),
				'shipper_address' => $request->input('shipper_address'),
				'shipper_phone' => $request->input('standard_phone'),
				'shipper_id' => $request->input('shipper_id'),
				'consignee_name' => $request->input('consignee_name'),
				'consignee_address' => $request->input('consignee_address'),
				'consignee_phone' => $request->input('consignee_phone'),
				'consignee_id' => $request->input('consignee_id'),
				'length' => $request->input('length'),
				'width' => $request->input('width'),
				'height' => $request->input('height'),
				'weight' => $request->input('weight'),
				'items' => $request->input('shipped_items'),
				'shipment_val' => $request->input('shipment_val')
			]);			
			// End Update Old Packing Eng
		}	
		
		return redirect()->to(session('this_previous_url'))->with('status', 'Row updated successfully!');
	}


	public function destroy(Request $request)
	{
		$id = $request->input('action');

		EngDraftWorksheet::where('id', $id)->delete();
		PackingEng::where('work_sheet_id', $id)->delete();

		return redirect()->to(session('this_previous_url'))->with('status', 'Row deleted successfully!');
	}


    public function addEngDraftDataById(Request $request){
    	$row_arr = $request->input('row_id');
    	$value_by = $request->input('value-by-tracking');
    	$column = $request->input('phil-ind-tracking-columns');
    	$user = Auth::user();
    	$operator_change_row_arr = [];

    	for ($i=0; $i < count($row_arr); $i++) { 
    		$eng_draft_worksheet = EngDraftWorksheet::find($row_arr[$i]);
    		if (!$eng_draft_worksheet->operator) {
    			$operator_change_row_arr[] = $row_arr[$i];
    		}
    	}

    	if ($row_arr) {
    		if ($value_by && $column) {
    			if ($column !== 'operator') {
    				EngDraftWorksheet::whereIn('id', $row_arr)
    				->update([
    					$column => $value_by
    				]);
    			}
    			elseif($column === 'operator' && ($user->role === 'admin' || $user->role === 'office_1')){
    				EngDraftWorksheet::whereIn('id', $row_arr)
    				->update([
    					'operator' => $value_by
    				]);
    			} 


    			// Update Update Old Packing Eng
    			$params_arr = ['shipper_name','shipper_address','shipper_id','consignee_name','consignee_address','consignee_phone','consignee_id','length','width','height','weight','shipment_val'];
    			if ($column === 'shipped_items') {
    				PackingEng::whereIn('work_sheet_id', $row_arr)
    				->update([
    					'items' => $value_by
    				]);
    			}
    			if ($column === 'standard_phone') {
    				PackingEng::whereIn('work_sheet_id', $row_arr)
    				->update([
    					'shipper_phone' => $value_by
    				]);
    			}
    			if ($column === 'consignee_address') {
    				$address = explode(' ',$value_by);
    				PackingEng::whereIn('work_sheet_id', $row_arr)
    				->update([
    					'country' => $address[0],
    					$column => $value_by
    				]);
    			}
    			if (in_array($column, $params_arr)) {
    				PackingEng::whereIn('work_sheet_id', $row_arr)
    				->update([
    					$column => $value_by
    				]);
    			}
    			// End Update Old Packing Eng     	
    		
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

		EngDraftWorksheet::whereIn('id', $row_arr)->delete();
		PackingEng::whereIn('work_sheet_id', $row_arr)->delete();

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
        
        return view('admin.draft.eng_draft_worksheet', ['title' => $title,'data' => $data,'eng_draft_worksheet_obj' => $eng_draft_worksheet_obj, 'user' => $user, 'viewer_arr' => $viewer_arr]);
    }


    public function engDraftCheckActivate($id){
    	$eng_draft_worksheet = EngDraftWorksheet::find($id);
		$packing = PackingEng::where('work_sheet_id',$id)->first();
		$country = '';
		$error_message = 'Fill in required fields: ';

		if ($packing) $country = $packing->country;
		if ($country && $country === 'India') {
			if (!$eng_draft_worksheet->shipper_name) $error_message .= 'Shipper\'s name,';
			if (!$eng_draft_worksheet->shipper_address) $error_message .= 'Shipper\'s address,';
			if (!$eng_draft_worksheet->standard_phone) $error_message .= 'Shipper\'s phone (standard),';
			if (!$eng_draft_worksheet->consignee_name) $error_message .= 'Consignee\'s name,';
			if (!$eng_draft_worksheet->consignee_address) $error_message .= 'Consignee\'s address,';
			if (!$eng_draft_worksheet->consignee_phone) $error_message .= 'Consignee\'s phone number,';
			if (!$eng_draft_worksheet->house_name) $error_message .= 'House name,';
			if (!$eng_draft_worksheet->state_pincode) $error_message .= 'State pincode,';
			if (!$eng_draft_worksheet->post_office) $error_message .= 'Local post office,';
			if (!$eng_draft_worksheet->district) $error_message .= 'District/City,';

			if ($error_message !== 'Fill in required fields: ') {
				return response()->json(['error' => $error_message]);
			}			
		}
		elseif ($country && $country !== 'India') {
			if (!$eng_draft_worksheet->shipper_name) $error_message .= 'Shipper\'s name,';
			if (!$eng_draft_worksheet->shipper_address) $error_message .= 'Shipper\'s address,';
			if (!$eng_draft_worksheet->standard_phone) $error_message .= 'Shipper\'s phone (standard),';
			if (!$eng_draft_worksheet->consignee_name) $error_message .= 'Consignee\'s name,';
			if (!$eng_draft_worksheet->consignee_address) $error_message .= 'Consignee\'s address,';
			if (!$eng_draft_worksheet->consignee_phone) $error_message .= 'Consignee\'s phone number,';

			if ($error_message !== 'Fill in required fields: ') {
				return response()->json(['error' => $error_message]);
			}
		}
    	
    	$phone_exist = PhilIndWorksheet::where('standard_phone',$eng_draft_worksheet->standard_phone)->get()->last();

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


    public function engDraftActivate($id, Request $request)
	{
		$eng_draft_worksheet = EngDraftWorksheet::find($id);				
		$fields = $this->getTableColumns('eng_draft_worksheet');	
		$user = Auth::user();					

		if ($eng_draft_worksheet->parcels_qty && (int)$eng_draft_worksheet->parcels_qty > 1) {
			
			for ($i=0; $i < (int)$eng_draft_worksheet->parcels_qty; $i++) {
				$worksheet = new PhilIndWorksheet();
			
				foreach($fields as $field){
					if ($field !== 'created_at' && $field !== 'id' && $field !== 'parcels_qty') {
						$worksheet->$field = $eng_draft_worksheet->$field;
					}			
				}
				
				if ($user->role === 'office_1' || $request->input('color')) {
					$worksheet->background = 'tr-orange';
				}
				
				$worksheet->status_date = date('Y-m-d');
				
				if ($worksheet->save())	{
	
					$work_sheet_id = $worksheet->id;
	
					// New Packing Eng		
					$packing_fields = $this->getTableColumns('packing_eng');			
					$packing = PackingEng::where('work_sheet_id', $id)->get();
	
					$packing->each(function ($item, $key) use($work_sheet_id, $packing_fields) {
						$new_packing = new PackingEngNew();
						for ($i=0; $i < count($packing_fields); $i++) { 
							if ($packing_fields[$i] !== 'work_sheet_id' && $packing_fields[$i] !== 'id') {
								$new_packing[$packing_fields[$i]] = $item[$packing_fields[$i]];
							}
							else{
								$new_packing->work_sheet_id = $work_sheet_id;
							}					
						}
						$new_packing->save();
					});					
	
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

				}
			}
			EngDraftWorksheet::where('id', $id)->delete();
			PackingEng::where('work_sheet_id', $id)->delete();
			return redirect()->to(session('this_previous_url'))->with('status', 'Row activated successfully!');
		}
		else{
			$worksheet = new PhilIndWorksheet();

			foreach($fields as $field){
				if ($field !== 'created_at' && $field !== 'id' && $field !== 'parcels_qty') {
					$worksheet->$field = $eng_draft_worksheet->$field;
				}			
			}
			
			if ($user->role === 'office_1' || $request->input('color')) {
				$worksheet->background = 'tr-orange';
			}
			
			$worksheet->status_date = date('Y-m-d');
			
			if ($worksheet->save())	{

				$work_sheet_id = $worksheet->id;

				// New Packing Eng		
				$packing_fields = $this->getTableColumns('packing_eng');			
				$packing = PackingEng::where('work_sheet_id', $id)->get();

				$packing->each(function ($item, $key) use($work_sheet_id, $packing_fields) {
					$new_packing = new PackingEngNew();
					for ($i=0; $i < count($packing_fields); $i++) { 
						if ($packing_fields[$i] !== 'work_sheet_id' && $packing_fields[$i] !== 'id') {
							$new_packing[$packing_fields[$i]] = $item[$packing_fields[$i]];
						}
						else{
							$new_packing->work_sheet_id = $work_sheet_id;
						}					
					}
					$new_packing->save();
				});
				PackingEng::where('work_sheet_id', $id)->delete();

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
				return redirect()->to(session('this_previous_url'))->with('status-error', 'Activate error!');
			}
		}				
	}


	public function exportExcel()
	{

		return Excel::download(new EngDraftWorksheetExport, 'EngDraftWorksheetExport.xlsx');

	}

}
