<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Imports\ChecklistImport;
use App\Checklist;
use Excel;
use DB;
use App\Exports\DataExport;

class ChecklistController extends Controller
{
    public function index()
    {
        $update_date = Date('Y-m-d H:i', strtotime('+3 houers'));
        $title = 'Checklist';
        $checklist_obj = Checklist::paginate(10);
        return view('admin.checklist', compact('title','checklist_obj'));
    }
    

    /**
    * Uploads the records in a csv file or excel using maatwebsite package 
    *
    * @param Request $request
    * @return mixed
    */
    public function importChecklist(Request $request)
    {
        if ($request->hasFile('import_file')) {
            $file = $request->file('import_file');
            $import = new ChecklistImport();    
            $array = Excel::toArray($import, $file); 
            $result = $import->checkRow($array[0][0]);

            if (!$result) return redirect()->to(session('this_previous_url'))->with('status-error', 'Fields "tracking_main" and "value" are required!');     

            if (Schema::hasTable('checklist')) Schema::dropIfExists('checklist');
            Schema::create('checklist', function (Blueprint $table) {
                $table->increments('id');
                $table->string('tracking_main')->nullable();
                $table->string('value')->nullable();
                $table->timestamps();
            }); 

            Excel::import($import, $file);                       

            return redirect()->to(session('this_previous_url'))->with('status', 'File uploaded successfully!');
        } else {
            return redirect()->to(session('this_previous_url'))->with('status-error', 'File did not upload!');
        }
    }


    public function checksHistory()
    {
        $title = 'История';
        $checks_history = DB::table('checks_history')->select('list_name')->groupBy('list_name')->get();
        return view('admin.checks_history', compact('title','checks_history'));
    }


    public function exportChecksHistory(Request $request)
    {
        return Excel::download(new DataExport('checks_history',$request->list_name), 'checks_history_'.$request->list_name.'.csv');
    }


    public function destroy(Request $request)
    {
        $list_name = $request->action;
        DB::table('checks_history')->where('list_name', $list_name)->delete();
        return redirect()->to(session('this_previous_url'))->with('status', 'List deleted successfully!');
    }

}
