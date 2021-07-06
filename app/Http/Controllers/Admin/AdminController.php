<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NewWorksheet;
use App\PhilIndWorksheet;
use DB;

class AdminController extends Controller
{
	const ROLES_ARR = array('user' => 'user', 'warehouse' => 'warehouse', 'office_1' => 'office_1','office_2' => 'office_2', 'admin' => 'admin', 'viewer' => 'viewer', 'china_admin' => 'china_admin', 'china_viewer' => 'china_viewer', 'office_eng' => 'office_eng', 'viewer_eng' => 'viewer_eng', 'viewer_1' => 'viewer_1', 'viewer_2' => 'viewer_2', 'viewer_3' => 'viewer_3', 'viewer_4' => 'viewer_4', 'viewer_5' => 'viewer_5');
	const VIEWER_ARR = array('viewer_1', 'viewer_2', 'viewer_3', 'viewer_4', 'viewer_5');
	
	protected function new_columns(){

		$arr_columns = [];
		$new_column_1 = '';
        $new_column_2 = '';
        $new_column_3 = '';
        $new_column_4 = '';
        $new_column_5 = '';

		if (Schema::hasColumn('new_worksheet', 'new_column_1'))
		{
			$new_column_1 = 'Дополнительная колонка 1';
		}
		$arr_columns[] = $new_column_1;
		
		if (Schema::hasColumn('new_worksheet', 'new_column_2'))
		{
			$new_column_2 = 'Дополнительная колонка 2';
		}
		$arr_columns[] = $new_column_2;
		
		if (Schema::hasColumn('new_worksheet', 'new_column_3'))
		{
			$new_column_3 = 'Дополнительная колонка 3';
		}
		$arr_columns[] = $new_column_3;
		
		if (Schema::hasColumn('new_worksheet', 'new_column_4'))
		{
			$new_column_4 = 'Дополнительная колонка 4';
		}
		$arr_columns[] = $new_column_4;
		
		if (Schema::hasColumn('new_worksheet', 'new_column_5'))
		{
			$new_column_5 = 'Дополнительная колонка 5';
		}
		$arr_columns[] = $new_column_5;

		return $arr_columns;
	}


	protected function new_china_columns(){

		$arr_columns = [];
		$new_column_1 = '';
        $new_column_2 = '';
        $new_column_3 = '';
        $new_column_4 = '';
        $new_column_5 = '';

		if (Schema::hasColumn('china_worksheet', 'new_column_1'))
		{
			$new_column_1 = 'Additional column 1';
		}
		$arr_columns[] = $new_column_1;
		
		if (Schema::hasColumn('china_worksheet', 'new_column_2'))
		{
			$new_column_2 = 'Additional column 2';
		}
		$arr_columns[] = $new_column_2;
		
		if (Schema::hasColumn('china_worksheet', 'new_column_3'))
		{
			$new_column_3 = 'Additional column 3';
		}
		$arr_columns[] = $new_column_3;
		
		if (Schema::hasColumn('china_worksheet', 'new_column_4'))
		{
			$new_column_4 = 'Additional column 4';
		}
		$arr_columns[] = $new_column_4;
		
		if (Schema::hasColumn('china_worksheet', 'new_column_5'))
		{
			$new_column_5 = 'Additional column 5';
		}
		$arr_columns[] = $new_column_5;

		return $arr_columns;
	}


	protected function new_phil_ind_columns(){

		$arr_columns = [];
		$new_column_1 = '';
        $new_column_2 = '';
        $new_column_3 = '';
        $new_column_4 = '';
        $new_column_5 = '';

		if (Schema::hasColumn('phil_ind_worksheet', 'new_column_1'))
		{
			$new_column_1 = 'Additional column 1';
		}
		$arr_columns[] = $new_column_1;
		
		if (Schema::hasColumn('phil_ind_worksheet', 'new_column_2'))
		{
			$new_column_2 = 'Additional column 2';
		}
		$arr_columns[] = $new_column_2;
		
		if (Schema::hasColumn('phil_ind_worksheet', 'new_column_3'))
		{
			$new_column_3 = 'Additional column 3';
		}
		$arr_columns[] = $new_column_3;
		
		if (Schema::hasColumn('phil_ind_worksheet', 'new_column_4'))
		{
			$new_column_4 = 'Additional column 4';
		}
		$arr_columns[] = $new_column_4;
		
		if (Schema::hasColumn('phil_ind_worksheet', 'new_column_5'))
		{
			$new_column_5 = 'Additional column 5';
		}
		$arr_columns[] = $new_column_5;

		return $arr_columns;
	}


	protected function getTableColumns($table)
	{		
		return Schema::getColumnListing($table);
	}

}
