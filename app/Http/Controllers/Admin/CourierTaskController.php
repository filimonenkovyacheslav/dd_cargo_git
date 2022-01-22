<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;
use App\CourierTask;
use App\Exports\CourierTaskExport;
use Excel;


class CourierTaskController extends AdminController
{
	public function import()
	{
		$tasks = new CourierTask();
		return $tasks->importWorksheet();
	}
	

	public function index()
	{
		$title = 'Задания Курьерам/Couriers Tasks';
		$couriers_tasks_obj = CourierTask::paginate(10);
		return view('admin.couriers_tasks.couriers_tasks', compact('title', 'couriers_tasks_obj'));
	}


	public function courierTaskFilter(Request $request)
	{
        $title = 'Couriers Tasks Filter';
        $search = $request->table_filter_value;
        $filter_arr = [];
        $attributes = CourierTask::first()->attributesToArray();

        $id_arr = [];
        $new_arr = [];      

        if ($request->table_columns) {
        	$couriers_tasks_obj = CourierTask::where($request->table_columns, 'like', '%'.$search.'%')->paginate(10);
        }
        else{
        	foreach($attributes as $key => $value)
        	{
        		if ($key !== 'created_at' && $key !== 'updated_at') {
        			$sheet = CourierTask::where($key, 'like', '%'.$search.'%')->get()->first();
        			if ($sheet) {       				
        				$temp_arr = CourierTask::where($key, 'like', '%'.$search.'%')->get();
        				$new_arr = $temp_arr->filter(function ($item, $k) use($id_arr) {
        					if (!in_array($item->id, $id_arr)) { 
        						$id_arr[] = $item->id;       						  
        						return $item;    					
        					}       					       					
        				});        				
        				$filter_arr[] = $new_arr;   				         		
        			}
        		}         		
        	}

        	return view('admin.couriers_tasks.couriers_tasks_find', compact('title','filter_arr'));      	
        }
        
        $data = $request->all();             
        
        return view('admin.couriers_tasks.couriers_tasks', compact('title','couriers_tasks_obj','data'));
    }


    public function courierTaskDone($id)
    {
    	$task = CourierTask::find($id);
    	$done = $task->taskDone();
    	return redirect()->to(session('this_previous_url'))->with('status', 'Задание отмечено как выполненное / Task marked as completed!');
    }


    public function doneById(Request $request)
    {
    	$row_arr = $request->input('row_id');
		for ($i=0; $i < count($row_arr); $i++) { 
			$task = CourierTask::find($row_arr[$i]);
			$done = $task->taskDone();
		}
    	return redirect()->to(session('this_previous_url'))->with('status', 'Задания отмечены как выполненные / Tasks marked as completed!');
    }


	public function exportExcelCourierTask()
	{
		return Excel::download(new CourierTaskExport, 'CourierTaskExport.xlsx');
	}

}
