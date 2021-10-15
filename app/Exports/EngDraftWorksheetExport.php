<?php

namespace App\Exports;

use App\EngDraftWorksheet;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\Schema;

class EngDraftWorksheetExport implements FromQuery, WithHeadings
{  
	use Exportable;

    public function query()
    {
        return EngDraftWorksheet::query();
    } 
    
    public function headings(): array
    {
        return Schema::getColumnListing('eng_draft_worksheet');
    }
}