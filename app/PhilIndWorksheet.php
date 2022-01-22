<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\CourierTask;


class PhilIndWorksheet extends BaseModel
{
    protected $table = 'phil_ind_worksheet';
    protected $fillable = ['order_number', 'tracking_main', 'status', 'status_date', 'status_ru', 'status_he', 'operator','date','direction','tracking_local','pallet_number','comments_1','comments_2','shipper_name', 'shipper_city','passport_number','return_date','shipper_address','standard_phone','shipper_phone','shipper_id','consignee_name','house_name','post_office','district','state_pincode','consignee_address','consignee_phone','consignee_id','shipped_items','shipment_val','courier','delivery_date_comments','weight','width','height','length','volume_weight','lot','payment_date_comments','amount_payment','background','shipper_country','consignee_country','in_trash'];


    /**
    * Get the courier task associated with the eng worksheet.
    */
    public function courierTask()
    {
        return $this->hasOne('App\CourierTask','eng_worksheet_id');
    }


    /**
    * Check the courier task.
    */
    public function checkCourierTask($status)
    {
        $result = static::isNecessaryCourierTask($status,$this->table);
        if (!$result) {
            if ($this->courierTask) $this->courierTask->delete();
        }
        elseif(!$this->courierTask){
            $new_task = new CourierTask();
            $new_task->eng_worksheet_id = $this->id;
            $new_task->direction = $this->direction;
            $new_task->status = $this->status;
            $new_task->parcels_qty = 1;
            $new_task->comments_1 = $this->comments_1;
            $new_task->comments_2 = $this->comments_2;
            $new_task->shipper_name = $this->shipper_name;
            $new_task->shipper_country = $this->shipper_country;
            $new_task->shipper_city = $this->shipper_city;
            $new_task->shipper_address = $this->shipper_address;
            $new_task->standard_phone = $this->standard_phone;
            $new_task->courier = $this->courier;
            $new_task->pick_up_date_comments = $this->delivery_date_comments;
            $new_task->save();
            $result = $new_task;
        }
        elseif ($this->courierTask) {
            $this->courierTask->direction = $this->direction;
            $this->courierTask->status = $this->status;
            $this->courierTask->parcels_qty = 1;
            $this->courierTask->comments_1 = $this->comments_1;
            $this->courierTask->comments_2 = $this->comments_2;
            $this->courierTask->shipper_name = $this->shipper_name;
            $this->courierTask->shipper_country = $this->shipper_country;
            $this->courierTask->shipper_city = $this->shipper_city;
            $this->courierTask->shipper_address = $this->shipper_address;
            $this->courierTask->standard_phone = $this->standard_phone;
            $this->courierTask->courier = $this->courier;
            $this->courierTask->pick_up_date_comments = $this->delivery_date_comments;
            $this->courierTask->save();
            $result = $this->courierTask;
        }
        return $result;
    }
}
