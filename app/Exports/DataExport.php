<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\Schema;
use DB;

class DataExport implements FromQuery, WithHeadings
{  
    use Exportable;

    public function __construct(string $table_name, string $list_name = '')
    {
        $this->table_name = $table_name;
        $this->list_name = $list_name;
    }

    public function query()
    {
        if ($this->list_name) {
            return DB::table($this->table_name)->select('*')->where('list_name',$this->list_name)->orderBy('id');
        }
        else{
            return DB::table($this->table_name)->select('*')->orderBy('id');
        }        
    } 
    
    public function headings(): array
    {
        return array_keys(json_decode(json_encode(DB::table($this->table_name)->first()),true));
    }
}