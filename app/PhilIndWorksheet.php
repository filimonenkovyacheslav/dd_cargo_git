<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PhilIndWorksheet extends Model
{
    protected $table = 'phil_ind_worksheet';
    protected $fillable = ['order_number', 'tracking_main', 'status', 'status_date', 'status_ru', 'status_he', 'operator','date','direction','tracking_local','pallet_number','comments_1','comments_2','shipper_name', 'shipper_city','passport_number','return_date','shipper_address','standard_phone','shipper_phone','shipper_id','consignee_name','house_name','post_office','district','state_pincode','consignee_address','consignee_phone','consignee_id','shipped_items','shipment_val','courier','delivery_date_comments','weight','width','height','length','volume_weight','lot','payment_date_comments','amount_payment','background','shipper_country','consignee_country'];
}
