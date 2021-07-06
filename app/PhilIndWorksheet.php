<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PhilIndWorksheet extends Model
{
    protected $table = 'phil_ind_worksheet';
    protected $fillable = ['order_number', 'tracking_main', 'status', 'status_ru', 'status_he', 'operator'];
}
