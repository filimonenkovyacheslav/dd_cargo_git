<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Worksheet;
use Transliterate;
use DB;

class IndexController extends AdminController
{
    public function index(){
        $title = 'Партнеры';
        $partners = DB::select('select * from partners');
        $viewer_arr = parent::VIEWER_ARR;
        $id = Auth::user()->id;
        $role = User::find($id)->role;
        /*if (file_get_contents(url('/work-last.json'))) {
        	$worksheet = json_decode(file_get_contents(url('/work-last.json')));
        	$fields = ['num_row', 'date', 'direction', 'status', 'local', 'tracking', 'manager_comments', 'comment','comments', 'sender', 'data_sender', 'recipient', 'data_recipient', 'email_recipient', 'parcel_cost', 'packaging','pays_parcel', 'number_weight', 'width', 'height', 'length', 'batch_number', 'shipment_type', 'parcel_description','position_1', 'position_2', 'position_3', 'position_4', 'position_5', 'position_6', 'position_7', 'guarantee_text_en','guarantee_text_ru', 'guarantee_text_he', 'guarantee_text_ua', 'payment', 'phys_weight', 'volume_weight', 'quantity', 'comments_2','cost_price', 'shipment_cost'];
        	foreach ($worksheet as $key => $value){						
        		$empty = true;
        		foreach ($value as $k => $val){
        			if ($val) {
        				$empty = false;
        				break;
        			}					
        		}
        		if ($empty) continue;
        		$i = 0;
        		$j = 0;
        		$new_row = new Worksheet();
        		foreach ($value as $k => $val){
        			if ($i != 6 && $j != 42){
        				$new_row[$fields[$j]] = $val;
        				$j++;
        			}
        			$i++;
        		}
        		$new_row->save();
        	}
        }*/
        if ($role !== 'user' && $role !== 'china_viewer' && $role !== 'china_admin' && $role !== 'office_eng' && $role !== 'viewer_eng') {
            return view('admin.partners', ['title' => $title,'partners' => $partners, 'viewer_arr' => $viewer_arr]);
        }
        elseif ($role === 'china_viewer' || $role === 'china_admin') {
            return redirect()->route('adminChinaIndex');
        }
        elseif ($role === 'office_eng' || $role === 'viewer_eng') {
            return redirect()->route('adminPhilIndIndex');
        }
        else{
            return redirect()->route('welcome');
        }
    }


    public function chinaIndex(){
        $title = 'Users';
        $users = DB::select('select * from users');
        $id = Auth::user()->id;
        $role = User::find($id)->role;

        if ($role !== 'user') {
            return view('admin.china.china_users', ['title' => $title,'users' => $users]);
        }
        else{
            return redirect()->route('welcome');
        }
    }


    public function philIndIndex(){
        $title = 'Users';
        $users = DB::select('select * from users');
        $id = Auth::user()->id;
        $role = User::find($id)->role;

        if ($role !== 'user') {
            return view('admin.phil_ind.phil_ind_users', ['title' => $title,'users' => $users]);
        }
        else{
            return redirect()->route('welcome');
        }
    }
}
