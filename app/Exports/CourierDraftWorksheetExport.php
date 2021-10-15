<?php

namespace App\Exports;

use App\CourierDraftWorksheet;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\Schema;

class CourierDraftWorksheetExport implements FromQuery, WithHeadings
{  
	use Exportable;

    public function query()
    {
        return CourierDraftWorksheet::query();
    } 
    
    public function headings(): array
    {
        return Schema::getColumnListing('courier_draft_worksheet');
    }
}