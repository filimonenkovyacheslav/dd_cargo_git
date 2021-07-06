<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NewWorksheet extends Model
{   
    protected $table = 'new_worksheet';
    protected $fillable = ['order_number', 'tracking_main', 'status', 'status_en', 'status_he', 'status_ua', 'update_status_date'];
}
