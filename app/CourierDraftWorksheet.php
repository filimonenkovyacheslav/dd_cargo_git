<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CourierDraftWorksheet extends Model
{   
    protected $table = 'courier_draft_worksheet';
    protected $fillable = ['order_number', 'tracking_main', 'status', 'status_en', 'status_he', 'status_ua', 'update_status_date', 'date', 'standard_phone', 'site_name', 'package_content', 'direction', 'tariff', 'comment_2','comments','sender_name','sender_country','sender_city','sender_postcode','sender_address','sender_phone','sender_passport','recipient_name','recipient_country','region','district','recipient_city','recipient_postcode','recipient_street','recipient_house','body','recipient_room','recipient_phone','recipient_passport','recipient_email','courier','pick_up_date','weight','width','height','length','volume_weight','quantity_things'];
}
