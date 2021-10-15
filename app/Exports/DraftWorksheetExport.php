<?php

namespace App\Exports;

use App\DraftWorksheet;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\Schema;

class DraftWorksheetExport implements FromQuery, WithHeadings
{  
	use Exportable;

    public function query()
    {
        return DraftWorksheet::query();
    } 
    
    public function headings(): array
    {
        return Schema::getColumnListing('draft_worksheet');
    }
}