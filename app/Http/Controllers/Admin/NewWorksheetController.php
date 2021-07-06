<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;
use App\NewWorksheet;
use App\PackingSea;
use DB;
use Excel;
use App\Exports\NewWorksheetExport;
use App\Exports\PackingAirExport;
use App\Exports\PackingSeaExport;
use Auth;


class NewWorksheetController extends AdminController
{
    public function index(){
        $title = 'Новый рабочий лист';
        $new_worksheet_obj = NewWorksheet::paginate(10);     

        $arr_columns = parent::new_columns();

        $user = Auth::user();
        $viewer_arr = parent::VIEWER_ARR;

        $update_all_statuses = NewWorksheet::where('update_status_date','=', date('Y-m-d'))->get()->count();
        
        return view('admin.new_worksheet', ['title' => $title,'new_worksheet_obj' => $new_worksheet_obj,'new_column_1' => $arr_columns[0],'new_column_2' => $arr_columns[1],'new_column_3' => $arr_columns[2],'new_column_4' => $arr_columns[3],'new_column_5' => $arr_columns[4], 'user' => $user, 'viewer_arr' => $viewer_arr, 'update_all_statuses' => $update_all_statuses]);
    }


    public function showAdd()
	{
		return '<h1>Действие не доступно !</h1>';
		
		$arr_columns = parent::new_columns();
		$title = 'Добавление строки';
		return view('admin.new_worksheet_add', ['title' => $title,'new_column_1' => $arr_columns[0],'new_column_2' => $arr_columns[1],'new_column_3' => $arr_columns[2],'new_column_4' => $arr_columns[3],'new_column_5' => $arr_columns[4]]);
	}


	public function add(Request $request)
	{				
		$new_worksheet = new NewWorksheet();
		$arr_columns = parent::new_columns();
		$fields = $this->getTableColumns('new_worksheet');
		
		for ($i=0; $i < count($arr_columns); $i++) { 
			if ($arr_columns[$i]) {
				$fields[] = 'new_column_'.($i+1);
			}
		}

		foreach($fields as $field){
			if ($field !== 'created_at') {
				$new_worksheet->$field = $request->input($field);
			}			
		}

		if (!$new_worksheet->status) {
			$new_worksheet->status = 'Забрать';
		}
		
		if ($new_worksheet->save()) {
			
			// Adding order number
			if ($new_worksheet->standard_phone) {

				$standard_phone = ltrim($new_worksheet->standard_phone, " \+");

				$data = NewWorksheet::where('standard_phone', '+'.$standard_phone)
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
		
		return redirect()->to(session('this_previous_url'))->with('status', 'Строка успешно добавлена!');
	}


	public function show($id)
	{
		$arr_columns = parent::new_columns();
		$new_worksheet = NewWorksheet::find($id);
		$title = 'Изменение строки '.$new_worksheet->id;

		return view('admin.new_worksheet_update', ['title' => $title,'new_worksheet' => $new_worksheet,'new_column_1' => $arr_columns[0],'new_column_2' => $arr_columns[1],'new_column_3' => $arr_columns[2],'new_column_4' => $arr_columns[3],'new_column_5' => $arr_columns[4]]);
	}


	public function update(Request $request, $id)
	{
		$new_worksheet = NewWorksheet::find($id);
		$arr_columns = parent::new_columns();
		$fields = $this->getTableColumns('new_worksheet');

		for ($i=0; $i < count($arr_columns); $i++) { 
			if ($arr_columns[$i]) {
				$fields[] = 'new_column_'.($i+1);
			}
		}

		foreach($fields as $field){
			if ($field === 'tracking_main') {
				if ($request->input('tracking_main')) {
					DB::table('packing_sea')
					->where('work_sheet_id', $id)
					->update([
						'track_code' => $request->input('tracking_main')
					]);
				}
			}			
			if ($field !== 'created_at') {
				$new_worksheet->$field = $request->input($field);
			}
		}
		
		if ($new_worksheet->save()) {
			
			// Adding order number
			if ($new_worksheet->standard_phone) {

				$standard_phone = ltrim($new_worksheet->standard_phone, " \+");

				$data = NewWorksheet::where('standard_phone', '+'.$standard_phone)
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
		
		return redirect()->to(session('this_previous_url'))->with('status', 'Строка успешно обновлена!');
	}


	public function destroy(Request $request)
	{
		$id = $request->input('action');

		DB::table('new_worksheet')
		->where('id', '=', $id)
		->delete();

		return redirect()->to(session('this_previous_url'))->with('status', 'Строка успешно удалена!');
	}


	public function addColumn()
	{
		$message = 'Колонка успешно добавлена!';
		
		if (!Schema::hasColumn('new_worksheet', 'new_column_1'))
		{
			Schema::table('new_worksheet', function(Blueprint $table)
			{
				$table->string('new_column_1')->nullable();
			});
		}
		else if (!Schema::hasColumn('new_worksheet', 'new_column_2'))
		{
			Schema::table('new_worksheet', function(Blueprint $table)
			{
				$table->string('new_column_2')->nullable();
			});
		}
		else if (!Schema::hasColumn('new_worksheet', 'new_column_3'))
		{
			Schema::table('new_worksheet', function(Blueprint $table)
			{
				$table->string('new_column_3')->nullable();
			});
		}
		else if (!Schema::hasColumn('new_worksheet', 'new_column_4'))
		{
			Schema::table('new_worksheet', function(Blueprint $table)
			{
				$table->string('new_column_4')->nullable();
			});
		}
		else if (!Schema::hasColumn('new_worksheet', 'new_column_5'))
		{
			Schema::table('new_worksheet', function(Blueprint $table)
			{
				$table->string('new_column_5')->nullable();
			});
		}
		else
		{
			$message = 'Лимит колонок исчерпан!';
		}

		return redirect()->to(session('this_previous_url'))->with('status', $message);
	}


	public function deleteColumn(Request $request)
	{
		$name_column = $request->input('name_column');

		if ($name_column === 'new_column_1') {
			Schema::table('new_worksheet', function($table)
			{
				$table->dropColumn('new_column_1');
			});
		}
		elseif ($name_column === 'new_column_2') {
			Schema::table('new_worksheet', function($table)
			{
				$table->dropColumn('new_column_2');
			});
		}
		elseif ($name_column === 'new_column_3') {
			Schema::table('new_worksheet', function($table)
			{
				$table->dropColumn('new_column_3');
			});
		}
		elseif ($name_column === 'new_column_4') {
			Schema::table('new_worksheet', function($table)
			{
				$table->dropColumn('new_column_4');
			});
		}
		elseif ($name_column === 'new_column_5') {
			Schema::table('new_worksheet', function($table)
			{
				$table->dropColumn('new_column_5');
			});
		}

		return redirect()->to(session('this_previous_url'))->with('status', 'Колонка успешно удалена!');
	}


	public function showNewStatus(){
        $title = 'Изменение статусов по номеру партии';
        $worksheet_obj = NewWorksheet::all();
        $number_arr = [];
        foreach ($worksheet_obj as $row) {
        	if (!in_array($row->batch_number, $number_arr)) {
        		$number_arr[$row->batch_number] = $row->batch_number;
        	}
        }
        return view('admin.new_worksheet_status_number', ['title' => $title,'number_arr' => $number_arr]);
    }


    public function changeNewStatus(Request $request){
        if ($request->input('batch_number') && $request->input('status')) {
        	DB::table('new_worksheet')
        	->where('batch_number', $request->input('batch_number'))
          	->update([
          		'status' => $request->input('status'), 
          		'status_en' => $request->input('status_en'),
          		'status_ua' => $request->input('status_ua'),
          		'status_he' => $request->input('status_he')
          	]);
        }
        return redirect()->to(session('this_previous_url'));
    }


    public function showNewStatusDate(){
        $title = 'Изменение статусов по дате';
        $worksheet_obj = NewWorksheet::all();
        $date_arr = [];
        foreach ($worksheet_obj as $row) {
        	if (!in_array($row->date, $date_arr)) {
        		$date_arr[$row->date] = $row->date;
        	}
        }
        return view('admin.new_worksheet_status_date', ['title' => $title,'date_arr' => $date_arr]);
    }


    public function changeNewStatusDate(Request $request){
        if ($request->input('date') && $request->input('status')) {
        	DB::table('new_worksheet')
        	->where('date', $request->input('date'))
          	->update([
          		'status' => $request->input('status'), 
          		'status_en' => $request->input('status_en'),
          		'status_ua' => $request->input('status_ua'),
          		'status_he' => $request->input('status_he')
          	]);
        }
        return redirect()->to(session('this_previous_url'));
    }


    public function showNewData(){
        $title = 'Массовое изменение данных по трекингу (поддерживает массовое выделение чекбоксов)';
        $worksheet_obj = NewWorksheet::orderBy('tracking_main')->get();
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
        return view('admin.new_worksheet_tracking_data', ['title' => $title,'date_arr' => $date_arr]);
    }


    public function addNewData(Request $request){
    	$track_arr = $request->input('tracking');
    	$value_by = $request->input('value-by-tracking');
    	$column = $request->input('tracking-columns');
    	//echo '<pre>'.print_r($request->input('status_en'),true).'</pre>';
    	if ($track_arr) {
    		if ($value_by && $column) {
    			NewWorksheet::whereIn('tracking_main', $track_arr)
    			->update([
    				$column => $value_by
    			]);       	
    		}
    		else if ($request->input('status')){
    			NewWorksheet::whereIn('tracking_main', $track_arr)
    			->update([
    				'status' => $request->input('status'), 
    				'status_en' => $request->input('status_en'),
    				'status_ua' => $request->input('status_ua'),
    				'status_he' => $request->input('status_he')
    			]);
    		}
    		else if ($request->input('site_name')) {
    			NewWorksheet::whereIn('tracking_main', $track_arr)
    			->update([
    				'site_name' => $request->input('site_name')
    			]);       	
    		}
    		else if ($request->input('tariff')) {
    			NewWorksheet::whereIn('tracking_main', $track_arr)
    			->update([
    				'tariff' => $request->input('tariff')
    			]);       	
    		}
    		else if ($request->input('partner')) {
    			NewWorksheet::whereIn('tracking_main', $track_arr)
    			->update([
    				'partner' => $request->input('partner')
    			]);       	
    		}
    	}
        
        return redirect()->to(session('this_previous_url'));
    }


    public function addNewDataById(Request $request){
    	$row_arr = $request->input('row_id');
    	$value_by = $request->input('value-by-tracking');
    	$column = $request->input('tracking-columns');

    	if ($row_arr) {
    		if ($value_by && $column) {
    			NewWorksheet::whereIn('id', $row_arr)
    			->update([
    				$column => $value_by
    			]);       	
    		}
    		else if ($request->input('status')){
    			NewWorksheet::whereIn('id', $row_arr)
    			->update([
    				'status' => $request->input('status'), 
    				'status_en' => $request->input('status_en'),
    				'status_ua' => $request->input('status_ua'),
    				'status_he' => $request->input('status_he')
    			]);
    		}
    		else if ($request->input('site_name')) {
    			NewWorksheet::whereIn('id', $row_arr)
    			->update([
    				'site_name' => $request->input('site_name')
    			]);       	
    		}
    		else if ($request->input('tariff')) {
    			NewWorksheet::whereIn('id', $row_arr)
    			->update([
    				'tariff' => $request->input('tariff')
    			]);       	
    		}
    		else if ($request->input('partner')) {
    			NewWorksheet::whereIn('id', $row_arr)
    			->update([
    				'partner' => $request->input('partner')
    			]);       	
    		}
    	}
        
        return redirect()->to(session('this_previous_url'))->with('status', 'Строки успешно изменены!');
    }


    public function deleteNewWorksheetById(Request $request)
	{
		$row_arr = $request->input('row_id');

		NewWorksheet::whereIn('id', $row_arr)
		->delete();

		return redirect()->to(session('this_previous_url'))->with('status', 'Строки успешно удалены!');
	}


    public function exportExcel()
	{

    	return Excel::download(new NewWorksheetExport, 'NewWorksheetExport.xlsx');

	}


	public function indexPackingSea(){
        $title = 'Пакинг лист море';
        $packing_sea_obj = PackingSea::all();       
        
        return view('admin.packing.packing_sea', ['title' => $title,'packing_sea_obj' => $packing_sea_obj]);
    }


    public function showAddPackingSea()
	{
		$title = 'Добавление строки';
		return view('admin.packing.packing_sea_add', ['title' => $title]);
	}


	public function addPackingSea(Request $request)
	{
		$packing_sea = new PackingSea();

		$fields = ['payer', 'contract', 'type', 'track_code', 'full_shipper', 'full_consignee', 'country_code', 'postcode', 'region', 'district', 'city', 'street', 'house', 'body', 'room', 'phone', 'tariff', 'tariff_cent', 'weight_kg', 'weight_g', 'service_code', 'amount_1', 'amount_2', 'attachment_number', 'attachment_name', 'amount_3', 'weight_enclosures_kg', 'weight_enclosures_g', 'value_euro', 'value_cent', 'work_sheet_id'];

		foreach($fields as $field){
			$packing_sea->$field = $request->input($field);
		}

		$packing_sea->save();

		return redirect()->to(session('this_previous_url'))->with('status', 'Строка успешно добавлена!');
	}


	public function showPackingSea($id)
	{
		$packing_sea = PackingSea::find($id);
		$title = 'Изменение строки '.$packing_sea->id;

		return view('admin.packing.packing_sea_update', ['title' => $title,'packing_sea' => $packing_sea]);
	}


	public function updatePackingSea(Request $request, $id)
	{
		$packing_sea = PackingSea::find($id);

		$fields = ['payer', 'contract', 'type', 'track_code', 'full_shipper', 'full_consignee', 'country_code', 'postcode', 'region', 'district', 'city', 'street', 'house', 'body', 'room', 'phone', 'tariff', 'tariff_cent', 'weight_kg', 'weight_g', 'service_code', 'amount_1', 'amount_2', 'attachment_number', 'attachment_name', 'amount_3', 'weight_enclosures_kg', 'weight_enclosures_g', 'value_euro', 'value_cent', 'work_sheet_id'];

		foreach($fields as $field){
			$packing_sea->$field = $request->input($field);
		}
		
		$packing_sea->save();
		return redirect()->to(session('this_previous_url'))->with('status', 'Строка успешно обновлена!');
	}


	public function destroyPackingSea(Request $request)
	{
		$id = $request->input('action');

		DB::table('packing_sea')
		->where('id', '=', $id)
		->delete();

		return redirect()->to(session('this_previous_url'))->with('status', 'Строка успешно удалена!');
	}


	public function exportExcelPackingSea()
	{

    	return Excel::download(new PackingSeaExport, 'PackingSeaExport.xlsx');

	}


	public function newWorksheetFilter(Request $request){
        $title = 'Фильтр Нового рабочего листа';
        $search = $request->table_filter_value;
        $new_worksheet_arr = [];
        $attributes = NewWorksheet::first()->attributesToArray();

        $arr_columns = parent::new_columns();

        $user = Auth::user();
        $viewer_arr = parent::VIEWER_ARR; 
        $id_arr = [];
        $new_arr = [];      

        if ($request->table_columns) {
        	$new_worksheet_obj = NewWorksheet::where($request->table_columns, 'like', '%'.$search.'%')
        	->paginate(10);
        }
        else{
        	foreach($attributes as $key => $value)
        	{
        		if ($key !== 'created_at' && $key !== 'updated_at' && $key !== 'update_status_date') {
        			$sheet = NewWorksheet::where($key, 'like', '%'.$search.'%')->get()->first();
        			if ($sheet) {       				
        				$temp_arr = NewWorksheet::where($key, 'like', '%'.$search.'%')->get();
        				$new_arr = $temp_arr->filter(function ($item, $k) use($id_arr) {
        					if (!in_array($item->id, $id_arr)) { 
        						$id_arr[] = $item->id;       						  
        						return $item;    					
        					}       					       					
        				});        				
        				$new_worksheet_arr[] = $new_arr;   				         		
        			}
        		}         		
        	}

        	return view('admin.new_worksheet_find', ['title' => $title,'new_worksheet_arr' => $new_worksheet_arr,'new_column_1' => $arr_columns[0],'new_column_2' => $arr_columns[1],'new_column_3' => $arr_columns[2],'new_column_4' => $arr_columns[3],'new_column_5' => $arr_columns[4], 'user' => $user, 'viewer_arr' => $viewer_arr]);      	
        }
        
        $data = $request->all();             
        
        return view('admin.new_worksheet_filter', ['title' => $title,'data' => $data,'new_worksheet_obj' => $new_worksheet_obj,'new_column_1' => $arr_columns[0],'new_column_2' => $arr_columns[1],'new_column_3' => $arr_columns[2],'new_column_4' => $arr_columns[3],'new_column_5' => $arr_columns[4], 'user' => $user, 'viewer_arr' => $viewer_arr]);
    }

}
