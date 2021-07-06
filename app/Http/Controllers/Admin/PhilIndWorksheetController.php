<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;
use App\PhilIndWorksheet;
use DB;
use Excel;
use App\PackingEng;
use App\Exports\PhilIndWorksheetExport;
use App\Exports\PackingEngExport;
use Auth;


class PhilIndWorksheetController extends AdminController
{
    public function index(){
        $title = 'Work sheet';      
        $phil_ind_worksheet_obj = PhilIndWorksheet::paginate(10);
        $arr_columns = parent::new_phil_ind_columns();
        
        return view('admin.phil_ind.phil_ind_worksheet', ['title' => $title,'phil_ind_worksheet_obj' => $phil_ind_worksheet_obj,'new_column_1' => $arr_columns[0],'new_column_2' => $arr_columns[1],'new_column_3' => $arr_columns[2],'new_column_4' => $arr_columns[3],'new_column_5' => $arr_columns[4]]);
    }


    public function showAdd()
	{
		return '<h1>Action not available !</h1>';
		
		$arr_columns = parent::new_phil_ind_columns();
		$title = 'Add row';
		return view('admin.phil_ind.phil_ind_worksheet_add', ['title' => $title,'new_column_1' => $arr_columns[0],'new_column_2' => $arr_columns[1],'new_column_3' => $arr_columns[2],'new_column_4' => $arr_columns[3],'new_column_5' => $arr_columns[4]]);
	}


	public function add(Request $request)
	{
		$phil_ind_worksheet = new PhilIndWorksheet();
		$arr_columns = parent::new_phil_ind_columns();
		$fields = $this->getTableColumns('phil_ind_worksheet');
		
		for ($i=0; $i < count($arr_columns); $i++) { 
			if ($arr_columns[$i]) {
				$fields[] = 'new_column_'.($i+1);
			}
		}

		foreach($fields as $field){
			if ($field !== 'created_at'){
				$phil_ind_worksheet->$field = $request->input($field);
			}			
		}

		if (!$phil_ind_worksheet->status) {
			$phil_ind_worksheet->status = 'Pick up';
		}

		if ($phil_ind_worksheet->save()) {
			
			// Adding order number
			if ($phil_ind_worksheet->standard_phone) {

				$standard_phone = ltrim($phil_ind_worksheet->standard_phone, " \+");

				$data = PhilIndWorksheet::where('standard_phone', '+'.$standard_phone)
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
								['standard_phone', '+'.$standard_phone],
								['order_number', '<>', null]
							])->get()->last()->order_number);

							$i++;
							return $item->update(['order_number'=> $i]);
						}               
					});
				}
			}
		}		

		return redirect()->to(session('this_previous_url'))->with('status', 'Row added successfully!');
	}


	public function show($id)
	{
		$arr_columns = parent::new_phil_ind_columns();
		$phil_ind_worksheet = PhilIndWorksheet::find($id);
		$title = 'Update row '.$phil_ind_worksheet->id;

		return view('admin.phil_ind.phil_ind_worksheet_update', ['title' => $title,'phil_ind_worksheet' => $phil_ind_worksheet,'new_column_1' => $arr_columns[0],'new_column_2' => $arr_columns[1],'new_column_3' => $arr_columns[2],'new_column_4' => $arr_columns[3],'new_column_5' => $arr_columns[4]]);
	}


	public function update(Request $request, $id)
	{
		$phil_ind_worksheet = PhilIndWorksheet::find($id);
		$arr_columns = parent::new_phil_ind_columns();
		$fields = $this->getTableColumns('phil_ind_worksheet');
		$operator_change = true;
		$user = Auth::user();

		for ($i=0; $i < count($arr_columns); $i++) { 
			if ($arr_columns[$i]) {
				$fields[] = 'new_column_'.($i+1);
			}
		}

		if ($phil_ind_worksheet->operator) $operator_change = false;

		foreach($fields as $field){
			if ($field === 'tracking_main') {
				if ($request->input('tracking_main')) {
					DB::table('packing_eng')
					->where('work_sheet_id', $id)
					->update([
						'tracking' => $request->input('tracking_main')
					]);
				}
			}
			if ($field !== 'created_at' && $field !== 'operator'){
				$phil_ind_worksheet->$field = $request->input($field);
			}
			elseif ($field === 'operator' && $user->role === 'admin') {
				$phil_ind_worksheet->$field = $request->input($field);
			}
			elseif ($field === 'operator' && $user->role !== 'admin' && $operator_change) {
				$phil_ind_worksheet->$field = $request->input($field);
			}
		}
		
		if ($phil_ind_worksheet->save()) {
			
			// Adding order number
			if ($phil_ind_worksheet->standard_phone) {

				$standard_phone = ltrim($phil_ind_worksheet->standard_phone, " \+");

				$data = PhilIndWorksheet::where('standard_phone', '+'.$standard_phone)
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
								['standard_phone', '+'.$standard_phone],
								['order_number', '<>', null]
							])->get()->last()->order_number);

							$i++;
							return $item->update(['order_number'=> $i]);
						}               
					});
				}
			}
		}		
		
		return redirect()->to(session('this_previous_url'))->with('status', 'Row updated successfully!');
	}


	public function destroy(Request $request)
	{
		$id = $request->input('action');

		DB::table('phil_ind_worksheet')
		->where('id', '=', $id)
		->delete();

		return redirect()->to(session('this_previous_url'))->with('status', 'Row deleted successfully!');
	}


	public function addColumn()
	{
		$message = 'Column added successfully!';
		
		if (!Schema::hasColumn('phil_ind_worksheet', 'new_column_1'))
		{
			Schema::table('phil_ind_worksheet', function(Blueprint $table)
			{
				$table->string('new_column_1')->nullable();
			});
		}
		else if (!Schema::hasColumn('phil_ind_worksheet', 'new_column_2'))
		{
			Schema::table('phil_ind_worksheet', function(Blueprint $table)
			{
				$table->string('new_column_2')->nullable();
			});
		}
		else if (!Schema::hasColumn('phil_ind_worksheet', 'new_column_3'))
		{
			Schema::table('phil_ind_worksheet', function(Blueprint $table)
			{
				$table->string('new_column_3')->nullable();
			});
		}
		else if (!Schema::hasColumn('phil_ind_worksheet', 'new_column_4'))
		{
			Schema::table('phil_ind_worksheet', function(Blueprint $table)
			{
				$table->string('new_column_4')->nullable();
			});
		}
		else if (!Schema::hasColumn('phil_ind_worksheet', 'new_column_5'))
		{
			Schema::table('phil_ind_worksheet', function(Blueprint $table)
			{
				$table->string('new_column_5')->nullable();
			});
		}
		else
		{
			$message = 'The quantity of columns is limited!';
		}

		return redirect()->to(session('this_previous_url'))->with('status', $message);
	}


	public function deleteColumn(Request $request)
	{
		$name_column = $request->input('name_column');

		if ($name_column === 'new_column_1') {
			Schema::table('phil_ind_worksheet', function($table)
			{
				$table->dropColumn('new_column_1');
			});
		}
		elseif ($name_column === 'new_column_2') {
			Schema::table('phil_ind_worksheet', function($table)
			{
				$table->dropColumn('new_column_2');
			});
		}
		elseif ($name_column === 'new_column_3') {
			Schema::table('phil_ind_worksheet', function($table)
			{
				$table->dropColumn('new_column_3');
			});
		}
		elseif ($name_column === 'new_column_4') {
			Schema::table('phil_ind_worksheet', function($table)
			{
				$table->dropColumn('new_column_4');
			});
		}
		elseif ($name_column === 'new_column_5') {
			Schema::table('phil_ind_worksheet', function($table)
			{
				$table->dropColumn('new_column_5');
			});
		}

		return redirect()->to(session('this_previous_url'))->with('status', 'Column deleted successfully!');
	}


	public function showPhilIndStatus(){
        $title = 'Changing statuses by lot number';
        $worksheet_obj = PhilIndWorksheet::all();
        $number_arr = [];
        foreach ($worksheet_obj as $row) {
        	if (!in_array($row->lot, $number_arr)) {
        		$number_arr[$row->lot] = $row->lot;
        	}
        }
        return view('admin.phil_ind.phil_ind_worksheet_status_number', ['title' => $title,'number_arr' => $number_arr]);
    }


    public function changePhilIndStatus(Request $request){
        if ($request->input('lot_number') && $request->input('status')) {
        	DB::table('phil_ind_worksheet')
        	->where('lot', $request->input('lot_number'))
          	->update([
          		'status' => $request->input('status'), 
          		'status_he' => $request->input('status_he'),
          		'status_ru' => $request->input('status_ru')
          	]);
        }
        return redirect()->to(session('this_previous_url'));
    }


    public function showPhilIndStatusDate(){
        $title = 'Changing statuses by date';
        $worksheet_obj = PhilIndWorksheet::all();
        $date_arr = [];
        foreach ($worksheet_obj as $row) {
        	if (!in_array($row->date, $date_arr)) {
        		$date_arr[$row->date] = $row->date;
        	}
        }
        return view('admin.phil_ind.phil_ind_worksheet_status_date', ['title' => $title,'date_arr' => $date_arr]);
    }


    public function changePhilIndStatusDate(Request $request){
        if ($request->input('date') && $request->input('status')) {
        	DB::table('phil_ind_worksheet')
        	->where('date', $request->input('date'))
          	->update([
          		'status' => $request->input('status'), 
          		'status_he' => $request->input('status_he'),
          		'status_ru' => $request->input('status_ru')
          	]);
        }
        return redirect()->to(session('this_previous_url'));
    }


    public function showPhilIndData(){
        $title = 'Mass change of data by tracking number (supports mass selection of checkboxes)';
        $worksheet_obj = PhilIndWorksheet::orderBy('tracking_main')->get();
        $date_arr = [];
        foreach ($worksheet_obj as $row) {
        	$temp = $row->tracking_main;
        	if (strripos($row->tracking_main, ', ') !== false) {
        		$temp = explode(', ', $row->tracking_main)[0];
        	}
        	if (is_numeric($temp)) {
        		if (!in_array($row->tracking_main, $date_arr)) {
        			$date_arr[$row->tracking_main] = $row->tracking_main;
        		}
        	}
        	if (strripos($temp, 'IN') !== false || strripos($temp, 'PH') !== false || strripos($temp, 'NE') !== false || strripos($temp, 'CD') !== false) {
        		if (!in_array($row->tracking_main, $date_arr)) {
        			$date_arr[$row->tracking_main] = $row->tracking_main;
        		}
        	}
        }
        return view('admin.phil_ind.phil_ind_worksheet_tracking_data', ['title' => $title,'date_arr' => $date_arr]);
    }


    public function addPhilIndData(Request $request){
    	$track_arr = $request->input('tracking');
    	$value_by = $request->input('value-by-tracking');
    	$column = $request->input('phil-ind-tracking-columns');
    	//echo '<pre>'.print_r($request->input('status_en'),true).'</pre>';
    	if ($track_arr) {
    		if ($value_by && $column) {
    			PhilIndWorksheet::whereIn('tracking_main', $track_arr)
    			->update([
    				$column => $value_by
    			]);       	
    		}
    		else if ($request->input('status')){
    			PhilIndWorksheet::whereIn('tracking_main', $track_arr)
    			->update([
    				'status' => $request->input('status'), 
    				'status_ru' => $request->input('status_ru'),
    				'status_he' => $request->input('status_he')
    			]);
    		}
    	}
        
        return redirect()->to(session('this_previous_url'));
    }


    public function exportExcel()
	{

    	return Excel::download(new PhilIndWorksheetExport, 'PhilIndWorksheetExport.xlsx');

	}


	public function indexPackingEng(){
        $title = 'Packing List';
        $packing_eng_obj = PackingEng::all();       
        
        return view('admin.packing.packing_eng', ['title' => $title,'packing_eng_obj' => $packing_eng_obj]);
    }


    public function showAddPackingEng()
	{
		$title = 'Add row';
		return view('admin.packing.packing_eng_add', ['title' => $title]);
	}


	public function addPackingEng(Request $request)
	{
		$packing_eng = new PackingEng();

		$fields = ['tracking', 'country', 'shipper_name', 'shipper_address', 'shipper_phone', 'shipper_id', 'consignee_name', 'consignee_address', 'consignee_phone', 'consignee_id', 'length', 'width', 'height', 'weight', 'items', 'shipment_val', 'work_sheet_id'];

		foreach($fields as $field){
			$packing_eng->$field = $request->input($field);
		}

		$packing_eng->save();

		return redirect()->to(session('this_previous_url'))->with('status', 'Row added successfully!');
	}


	public function showPackingEng($id)
	{
		$packing_eng = PackingEng::find($id);
		$title = 'Update row '.$packing_eng->id;

		return view('admin.packing.packing_eng_update', ['title' => $title,'packing_eng' => $packing_eng]);
	}


	public function updatePackingEng(Request $request, $id)
	{
		$packing_eng = PackingEng::find($id);

		$fields = ['tracking', 'country', 'shipper_name', 'shipper_address', 'shipper_phone', 'shipper_id', 'consignee_name', 'consignee_address', 'consignee_phone', 'consignee_id', 'length', 'width', 'height', 'weight', 'items', 'shipment_val', 'work_sheet_id'];

		foreach($fields as $field){			
			$packing_eng->$field = $request->input($field);
		}
		
		$packing_eng->save();
		return redirect()->to(session('this_previous_url'))->with('status', 'Row updated successfully!');
	}


	public function destroyPackingEng(Request $request)
	{
		$id = $request->input('action');

		DB::table('packing_eng')
		->where('id', '=', $id)
		->delete();

		return redirect()->to(session('this_previous_url'))->with('status', 'Row deleted successfully!');
	}


	public function addPhilIndDataById(Request $request){
    	$row_arr = $request->input('row_id');
    	$value_by = $request->input('value-by-tracking');
    	$column = $request->input('phil-ind-tracking-columns');
    	$user = Auth::user();
    	$operator_change_row_arr = [];

    	for ($i=0; $i < count($row_arr); $i++) { 
    		$phil_ind_worksheet = PhilIndWorksheet::find($row_arr[$i]);
    		if (!$phil_ind_worksheet->operator) {
    			$operator_change_row_arr[] = $row_arr[$i];
    		}
    	}
		
    	if ($row_arr) {
    		if ($value_by && $column) {
    			if ($column !== 'operator' || $user->role === 'admin') {
    				PhilIndWorksheet::whereIn('id', $row_arr)
    				->update([
    					$column => $value_by
    				]);
    			}
    			elseif($column === 'operator' && count($operator_change_row_arr)){
    				PhilIndWorksheet::whereIn('id', $operator_change_row_arr)
    				->update([
    					'operator' => $value_by
    				]);
    			}       	
    		}
    		else if ($request->input('status')){
    			PhilIndWorksheet::whereIn('id', $row_arr)
    			->update([
    				'status' => $request->input('status'), 
    				'status_ru' => $request->input('status_ru'),
    				'status_he' => $request->input('status_he')
    			]);
    		}
    	}
        
        return redirect()->to(session('this_previous_url'))->with('status', 'Rows updated successfully!');
    }


    public function deletePhilIndWorksheetById(Request $request)
	{
		$row_arr = $request->input('row_id');

		PhilIndWorksheet::whereIn('id', $row_arr)
		->delete();

		return redirect()->to(session('this_previous_url'))->with('status', 'Rows deleted successfully!');
	}


	public function exportExcelPackingEng()
	{
    	return Excel::download(new PackingEngExport, 'PackingEngExport.xlsx');
	}


	public function philIndWorksheetFilter(Request $request)
	{
        $title = 'Work sheet Filter';
        $arr_columns = parent::new_phil_ind_columns();   
        $data = $request->all(); 
        $search = $request->table_filter_value;
        $worksheet_arr = []; 
        $attributes = PhilIndWorksheet::first()->attributesToArray(); 
        $id_arr = [];
        $new_arr = []; 
        
        if ($request->table_columns) {
        	$phil_ind_worksheet_obj = PhilIndWorksheet::where($request->table_columns, 'like', '%'.$search.'%')
        	->paginate(10);
        }
        else{
        	foreach($attributes as $key => $value)
        	{
        		if ($key !== 'created_at' && $key !== 'updated_at' && $key !== 'update_status_date') {
        			$sheet = PhilIndWorksheet::where($key, 'like', '%'.$search.'%')->get()->first();
        			if ($sheet) {
        				$temp_arr = PhilIndWorksheet::where($key, 'like', '%'.$search.'%')->get();
        				$new_arr = $temp_arr->filter(function ($item, $k) use($id_arr) {
        					if (!in_array($item->id, $id_arr)) { 
        						$id_arr[] = $item->id;       						  
        						return $item;    					
        					}       					       					
        				});        				
        				$worksheet_arr[] = $new_arr;         		
        			} 
        		}        		
        	}
        	
        	return view('admin.phil_ind.phil_ind_worksheet_find', ['title' => $title,'worksheet_arr' => $worksheet_arr,'new_column_1' => $arr_columns[0],'new_column_2' => $arr_columns[1],'new_column_3' => $arr_columns[2],'new_column_4' => $arr_columns[3],'new_column_5' => $arr_columns[4]]);
        }                       
        
        return view('admin.phil_ind.phil_ind_worksheet_filter', ['title' => $title,'data' => $data,'phil_ind_worksheet_obj' => $phil_ind_worksheet_obj,'new_column_1' => $arr_columns[0],'new_column_2' => $arr_columns[1],'new_column_3' => $arr_columns[2],'new_column_4' => $arr_columns[3],'new_column_5' => $arr_columns[4]]);
    }
}
