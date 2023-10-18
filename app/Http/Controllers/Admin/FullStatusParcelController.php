<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Imports\DataImport;
use App\Exports\DataExport;
use Excel;
use DB;


class FullStatusParcelController extends AdminController
{
    public function showFullStatusParcel()
    {
        $title = 'Full Status Parcel';
        $full_status_parcel_obj = DB::table('full_status_parcel')->paginate(10);
        return view('admin.full_status_parcel', compact('title','full_status_parcel_obj'));
    }
    

    /**
    * Uploads the records in a csv file or excel using maatwebsite package 
    *
    * @param Request $request
    * @return mixed
    */
    public function importFullStatusParcel(Request $request)
    {
        if ($request->hasFile('import_file')) {
            $file = $request->file('import_file');
            $import = new DataImport('full_status_parcel');     
            
            $array = Excel::toArray($import, $file); 
            $result = $import->checkRows($array[0][0]);
            if (!$result) {
                return redirect()->to(session('this_previous_url'))->with('status-error', 'Fields "tracking_main" and "value" are required!');  
            }

            DB::table('full_status_parcel')->delete();       
            
            Excel::import($import, $file);
            $result = $import->getRowCount();

               
            if ($result === -1) {
                return redirect()->to(session('this_previous_url'))->with('status-error', 'Error! Unknown column found!');
            }
            else {
                return redirect()->to(session('this_previous_url'))->with('status-success', $result.' rows imported successfully!');
            }
        
        } else {
            return redirect()->to(session('this_previous_url'))->with('status-error', 'File did not upload!');
        }
    }


    public function exportFullStatusParcel()
    {
        if (!DB::table('full_status_parcel')->first()) return redirect()->back()->with('status-error', 'Nothing to export!');
        return Excel::download(new DataExport('full_status_parcel'), 'full_status_parcel'.'.csv');
    }


    public function getFullStatusParcel(Request $request)
    {
        $tracking = $request->input('get_tracking');
        $message_arr['ru'] = '';
        $message_arr['en'] = '';
        $message_arr['he'] = '';
        $message_arr['ua'] = '';

        $row = DB::table('full_status_parcel')
        ->select('value')
        ->where([
            ['tracking_main', '=', $tracking]
        ])->get();

        if ($row->count()) {
            foreach ($row as $val) {
                if ($val->value) {
                    $message_arr['ru'] = $val->value;
                }
                if ($val->value) {
                    $message_arr['en'] = $val->value;
                }
                if ($val->value) {
                    $message_arr['he'] = $val->value;
                }
                if ($val->value) {
                    $message_arr['ua'] = $val->value;
                }
            }
        }

        return redirect()->route('fullStatusParcelForm')
        ->with( 'message_ru', $message_arr['ru'] )
        ->with( 'message_en', $message_arr['en'] )
        ->with( 'message_he', $message_arr['he'] )
        ->with( 'message_ua', $message_arr['ua'] )
        ->with( 'not_found', 'not_found' );
    }

}
