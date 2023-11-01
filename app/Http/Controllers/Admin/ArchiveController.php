<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;
use App\Archive;


class ArchiveController extends AdminController
{
	public function index()
	{
		$title = 'Archive';
		$archive_obj = Archive::paginate(10);
		return view('admin.archive.archive', compact('title', 'archive_obj'));
	}


	public function archiveFilter(Request $request)
	{
        $title = 'Archive Filter';
        $search = $request->table_filter_value;
        $filter_arr = [];
        $attributes = Archive::first()->attributesToArray();

        $id_arr = [];
        $new_arr = [];      

        if ($request->table_columns) {
        	$archive_obj = Archive::where($request->table_columns, 'like', '%'.$search.'%')->paginate(10);
        }
        else{
        	foreach($attributes as $key => $value)
        	{
        		if ($key !== 'created_at' && $key !== 'updated_at') {
        			$sheet = Archive::where($key, 'like', '%'.$search.'%')->get()->first();
        			if ($sheet) {       				
        				$temp_arr = Archive::where($key, 'like', '%'.$search.'%')->get();
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

        	return view('admin.archive.archive_find', compact('title','filter_arr'));      	
        }
        
        $data = $request->all();             
        
        return view('admin.archive.archive', compact('title','archive_obj','data'));
    }


    public function toArchive(Request $request)
    {
    	$archive = new Archive();
    	$id_arr = $archive->createArchive($request);
    	
        if ($id_arr) {
            $data = $request->all();
            $table = $data['table_name'];

            for ($i=0; $i < count($id_arr); $i++) { 
                $this->__toArchive($table, $id_arr[$i], $archive);
            } 		
        }

    	return redirect()->to(session('this_previous_url'))->with('status', 'Rows adds to archive!');
    }


    private function __toArchive($table, $id, $archive)
    {
    	switch($table) {
    		case 'new_worksheet';
    			$this->removeTrackingFromPalletWorksheet($id, 'ru');
                $this->deleteUploadFiles('worksheet_id',$id);
    			break;
    		case 'phil_ind_worksheet';
    			$this->removeTrackingFromPalletWorksheet($id, 'en');
                $this->deleteUploadFiles('eng_worksheet_id',$id);
    			break;      
    		default:
    		break;
    	}

        $archive->removeСompletely($id,$table);
    }


    public function deleteFromArchive($id)
    {
        /*$archive = Archive::find($id);
        $worksheet_id = $archive->worksheet_id;
        $table = $archive->table_name;      
        switch($table) {
            case 'new_worksheet';
                $this->removeTrackingFromPalletWorksheet($worksheet_id, 'ru');
                $this->deleteUploadFiles('worksheet_id',$worksheet_id);
                break;
            case 'phil_ind_worksheet';
                $this->removeTrackingFromPalletWorksheet($worksheet_id, 'en');
                $this->deleteUploadFiles('eng_worksheet_id',$worksheet_id);
                break;      
            default:
                break;
        }
        
        $archive->removeСompletely($id,$table);
        return redirect()->to(session('this_previous_url'))->with('status', 'Строка удалена / Row deleted!');*/
    }

}
