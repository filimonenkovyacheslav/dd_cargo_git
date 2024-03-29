<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use App\CourierTask;
use App\UpdatesArchive;
use App\SignedDocument;
use App\BaseModel;
use App\CourierEngDraftWorksheet;
use App\PackingEng;


class PhilIndWorksheet extends BaseModel
{
    public $table = 'phil_ind_worksheet';
    protected $fillable = ['order_number', 'tracking_main', 'status', 'status_date', 'status_ru', 'status_he', 'operator','date','direction','tracking_local','pallet_number','comments_1','comments_2','shipper_name', 'shipper_city','passport_number','return_date','shipper_address','standard_phone','shipper_phone','shipper_id','consignee_name','house_name','post_office','district','state_pincode','consignee_address','consignee_phone','consignee_id','shipped_items','shipment_val','courier','delivery_date_comments','weight','width','height','length','volume_weight','lot','payment_date_comments','amount_payment','background','shipper_country','consignee_country','in_trash','shipper_region','order_date','parcels_qty','packing_number'];


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
            $new_task->site_name = 'For';
            $new_task->status = $this->status;
            $new_task->parcels_qty = 1;
            $new_task->order_number = $this->order_number;
            $new_task->packing_num = $this->getLastDocUniq();
            $new_task->comments_1 = $this->comments_1;
            $new_task->comments_2 = $this->comments_2;
            $new_task->shipper_name = $this->shipper_name;
            $new_task->shipper_country = $this->shipper_country;
            $new_task->shipper_region = $this->shipper_region;
            $new_task->shipper_city = $this->shipper_city;
            $new_task->shipper_address = $this->shipper_address;
            $new_task->standard_phone = $this->standard_phone;
            $new_task->courier = $this->courier;
            $new_task->pick_up_date_comments = $this->delivery_date_comments;
            $new_task->weight = $this->weight;
            $new_task->shipped_items = $this->shipped_items;
            $new_task->save();
            $result = $new_task;
        }
        elseif ($this->courierTask) {
            $this->courierTask->direction = $this->direction;
            $this->courierTask->site_name = 'For';
            $this->courierTask->status = $this->status;
            $this->courierTask->parcels_qty = 1;
            $this->courierTask->order_number = $this->order_number;
            $this->courierTask->packing_num = $this->getLastDocUniq();
            $this->courierTask->comments_1 = $this->comments_1;
            $this->courierTask->comments_2 = $this->comments_2;
            $this->courierTask->shipper_name = $this->shipper_name;
            $this->courierTask->shipper_country = $this->shipper_country;
            $this->courierTask->shipper_region = $this->shipper_region;
            $this->courierTask->shipper_city = $this->shipper_city;
            $this->courierTask->shipper_address = $this->shipper_address;
            $this->courierTask->standard_phone = $this->standard_phone;
            $this->courierTask->courier = $this->courier;
            $this->courierTask->pick_up_date_comments = $this->delivery_date_comments;
            $this->courierTask->weight = $this->weight;
            $this->courierTask->shipped_items = $this->shipped_items;
            $this->courierTask->save();
            $result = $this->courierTask;
        }
        return $result;
    }
        

    /**
    * Get the updates archive associated with the eng worksheet.
    */
    public function updatesArchive()
    {
        return $this->hasOne('App\UpdatesArchive','eng_worksheet_id');
    }

   
    /**
    * Get the signed documents associated with the  eng worksheet.
    */
    public function signedDocuments()
    {
        return $this->hasMany('App\SignedDocument','eng_worksheet_id');
    }


    public function getLastDoc()
    {
        $documents = $this->signedDocuments;
        if ($documents->count() > 1) {
            foreach ($documents as $document) {
                if ($document->id == $documents->max('id')) {
                    return $document;
                }
            }
        }
        elseif($documents->count() == 1){
            foreach ($documents as $document) {
                if ($document->first_file) {
                    return $document;
                }
            }
        }
        else return null;
    }


    public function getLastDocUniq()
    {
        $document = $this->getLastDoc();
        if ($document) return $document->uniq_id;
        else return null;
    }


    public function deactivateWorksheet()
    {
        $draft = new CourierEngDraftWorksheet();
        $fields = Schema::getColumnListing('courier_eng_draft_worksheet');

        foreach($fields as $field){
            if ($field !== 'created_at' && $field !== 'id'){
                $draft->$field = $this->$field;
            }           
        }
        $draft->in_trash = false;                        

        if ($draft->save()) {          
            $work_sheet_id = $draft->id;
            $draft = CourierEngDraftWorksheet::find($work_sheet_id);

            // Packing
            $fields_packing = ['tracking', 'country', 'shipper_name', 'shipper_address', 'shipper_phone', 'shipper_id', 'consignee_name', 'consignee_address', 'consignee_phone', 'consignee_id', 'length', 'width', 'height', 'weight', 'items', 'shipment_val', 'work_sheet_id'];
            $packing = new PackingEng();
            foreach($fields_packing as $field){
                if ($field === 'country') {
                    $packing->$field = $this->consignee_country;
                    // New parcel form
                    if (!$this->consignee_address) $packing->consignee_address = $this->consignee_country;                    
                }
                elseif ($field === 'shipper_phone') {
                    $packing->$field = $this->standard_phone;
                }
                elseif ($field === 'work_sheet_id') {
                    $packing->$field = $work_sheet_id;
                }
                else if ($field === 'items') {
                    $packing->$field = $this->shipped_items;
                }
                else if ($field === 'tracking') {
                    $packing->$field = $this->tracking_main;
                }
                else{
                    $packing->$field = $this->$field;
                } 
            }
            $packing->save();
        }

        if ($draft) {
            return $draft;
        }
        else return null;
    }
}
