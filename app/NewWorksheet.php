<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NewWorksheet extends Model
{   
    protected $table = 'new_worksheet';
    protected $fillable = ['site_name','direction','order_number', 'tracking_main', 'status', 'status_date', 'status_en', 'status_he', 'status_ua', 'update_status_date','tariff','partner','tracking_local','tracking_transit','pallet_number','comment_2','comments','sender_name','sender_country','sender_city','sender_postcode','sender_address','sender_phone','sender_passport','recipient_name','recipient_country','region','district','recipient_city','recipient_postcode','recipient_street','recipient_house','body','recipient_room','recipient_phone','recipient_passport','recipient_email','package_cost','courier','pick_up_date','weight','width','height','length','volume_weight','quantity_things','batch_number','pay_date','pay_sum','background'];
}
