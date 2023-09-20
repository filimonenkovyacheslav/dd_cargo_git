<?php

namespace App\Imports;

use DB;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class DataImport implements ToCollection
{

    public function __construct(string $table_name)
    {
        $this->table_name = $table_name;
        $this->rows_qty = 0;
    }
    
    public function collection(Collection $rows)
    {
        $i = 0;
        $name_arr = [];
        foreach ($rows as $row) 
        {
            if ($i == 0) {
                $name_arr = $row;  
                if (!$this->checkRows($name_arr)) {
                    $this->rows_qty = -1;
                    return false;
                }
            }               
            else {
                $temp_arr = [];
                
                for ($i=0; $i < count($name_arr); $i++) { 
                    if ($name_arr[$i] === 'tracking_number') {
                        if ($row[$i] && DB::table($this->table_name)->where('tracking_number',$row[$i])->first())
                        return false;
                    }
                    elseif ($name_arr[$i] === 'id')
                        continue;
                    elseif ($name_arr[$i] === 'created_at')
                        $temp_arr[$name_arr[$i]] = date("Y-m-d H:i:s");
                    else
                        $temp_arr[$name_arr[$i]] = $row[$i];
                }

                DB::table($this->table_name)->insert($temp_arr);
                $this->rows_qty++;
            }                 
            $i++;
        }
    }

    public function getRowCount(): int
    {
        return $this->rows_qty;
    }

    public function checkRows($name_arr): bool
    {
        $result = false;
        //$attributes = array_keys(json_decode(json_encode(DB::table($this->table_name)->first()),true));
        $attributes = Schema::getColumnListing($this->table_name);
        for ($i=0; $i < count($name_arr); $i++) { 
            if (!in_array($name_arr[$i], $attributes)) {
                return $result;
            }
        }
        return $result = true;
    }
}