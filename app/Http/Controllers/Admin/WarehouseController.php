<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;
use App\Warehouse;
use App\PhilIndWorksheet;
use App\NewWorksheet;
use Auth;
use Excel;
use App\Exports\WarehouseExport;


class WarehouseController extends AdminController
{
    public function index(){
        $title = 'Warehouse';
        $warehouse_obj = Warehouse::paginate(10);     
        $user = Auth::user();
        
        return view('admin.warehouse.warehouse', ['title' => $title,'warehouse_obj' => $warehouse_obj, 'user' => $user]);
    }


	public function show($id)
	{
		$warehouse = Warehouse::find($id);
		$title = 'Update row '.$warehouse->id;

		return view('admin.warehouse.warehouse_update', ['title' => $title,'warehouse' => $warehouse]);
	}


	public function update(Request $request, $id)
	{
		$warehouse = Warehouse::find($id);
		$fields = $this->getTableColumns('warehouse');

		foreach($fields as $field){						
			if ($field !== 'created_at') {
				$warehouse->$field = $request->input($field);
			}
		}

		$warehouse->save();
		return redirect()->to(session('this_previous_url'))->with('status', 'Row updated successfully!');	
	}


	public function destroy(Request $request)
	{
		$id = $request->input('action');
		Warehouse::where('id', $id)->delete();
		return redirect()->to(session('this_previous_url'))->with('status', 'Row deleted successfully!');
	}


	public function warehouseFilter(Request $request){
        $title = 'Warehouse Filter';
        $search = $request->table_filter_value;
        $warehouse_arr = [];
        $attributes = Warehouse::first()->attributesToArray();
        $user = Auth::user(); 
        $id_arr = [];
        $new_arr = [];      

        if ($request->table_columns) {
        	$warehouse_obj = Warehouse::where($request->table_columns, 'like', '%'.$search.'%')
        	->paginate(10);
        }
        else{
        	foreach($attributes as $key => $value)
        	{
        		if ($key !== 'created_at' && $key !== 'updated_at' && $key !== 'update_status_date') {
        			$sheet = Warehouse::where($key, 'like', '%'.$search.'%')->get()->first();
        			if ($sheet) {       				
        				$temp_arr = Warehouse::where($key, 'like', '%'.$search.'%')->get();
        				$new_arr = $temp_arr->filter(function ($item, $k) use($id_arr) {
        					if (!in_array($item->id, $id_arr)) { 
        						$id_arr[] = $item->id;       						  
        						return $item;    					
        					}       					       					
        				});        				
        				$warehouse_arr[] = $new_arr;   				         		
        			}
        		}         		
        	}

        	return view('admin.warehouse.warehouse_find', ['title' => $title,'warehouse_arr' => $warehouse_arr, 'user' => $user]);      	
        }
        
        $data = $request->all();             
        
        return view('admin.warehouse.warehouse', ['title' => $title,'data' => $data,'warehouse_obj' => $warehouse_obj, 'user' => $user]);
    }


    public function warehouseOpen($id)
    {
    	$warehouse = Warehouse::find($id);
				
    }


	public function exportExcel()
	{
		return Excel::download(new WarehouseExport, 'WarehouseExport.xlsx');
	}

}
