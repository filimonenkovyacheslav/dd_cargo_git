<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EngDraftWorksheet extends Model
{   
    protected $table = 'eng_draft_worksheet';
    protected $fillable = ['order_number', 'tracking_main', 'status', 'status_ru', 'status_he'];
}
