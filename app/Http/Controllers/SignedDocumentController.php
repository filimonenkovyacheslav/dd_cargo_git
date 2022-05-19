<?php

namespace App\Http\Controllers;
  
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\SignedDocument;
use App\CourierDraftWorksheet;
use App\CourierEngDraftWorksheet;
use App\NewWorksheet;
use App\PhilIndWorksheet;
use App\PackingSea;
use App\PackingEng;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use PDF;
use DB;
  

class SignedDocumentController extends Controller
{
    public function getSignature()
    {
        $type = null;
        $id = null;
        $create_new = null;
        $cancel = null;
        $document = null;

        return view('pdf.signature_page',compact('type','id','create_new','cancel','document'));
    }


    public function formWithSignature(Request $request, $id, $token)
    {       
        $worksheet = CourierDraftWorksheet::find($id);
        if ($worksheet->getLastDoc()) return redirect()->to(session('this_previous_url'))->with('status-error', 'Document exists!');
        $israel_cities = $this->israelCities();
        $israel_cities['other'] = 'Другой город';
        $data_parcel = $this->fillResponseDataRu($worksheet, $request, true, true);
        if ($data_parcel) {
            $data_parcel = json_encode($data_parcel);
            DB::table('table_'.$token)
            ->insert([
                'data' => $data_parcel
            ]);
        }        

        return view('pdf.form_with_signature',compact('israel_cities','data_parcel','token','worksheet'));
    }


    public function formWithSignatureEng(Request $request, $id, $token)
    {
        $worksheet = CourierEngDraftWorksheet::find($id);
        if ($worksheet->getLastDoc()) return redirect()->to(session('this_previous_url'))->with('status-error', 'Document exists!'); 
        $israel_cities = $this->israelCities();
        $israel_cities['other'] = 'Other city';
        $data_parcel = $this->fillResponseDataEng($worksheet, $request, true, true);
        $data_parcel = json_encode($data_parcel);
        $domain = $this->getDomainRule();
        
        return view('pdf.form_with_signature_eng',compact('israel_cities','data_parcel','domain','token','worksheet'));       
    }


    public function signatureForCancel(Request $request)
    {
        $type = $request->type;
        $id = $request->id;
        $create_new = $request->create_new;
        $cancel = 'cancel';
        return view('pdf.signature_page',compact('type','id','create_new','cancel'));       
    }


    public function cancelingDocument($document)
    {
        $folderPath = $this->checkDirectory('canceled_documents');
        $oldPath = $this->checkDirectory('documents');
        $worksheet = $document->getWorksheet();
        $tracking = $worksheet->tracking_main;       
        $cancel = 'cancel';
        
        $old_document = SignedDocument::find($document->old_document_id);
        $file_name = $old_document->pdf_file;
        unlink($oldPath.$file_name);
        $document = $old_document;
        
        if (!$old_document->screen_ru_form) {
            if ($this->getDomainRule() !== 'forward') {
                $pdf = PDF::loadView('pdf.pdfview',compact('worksheet','document','tracking','cancel'));
            }
            elseif($this->getDomainRule() === 'forward'){
                $pdf = PDF::loadView('pdf.pdfview_forward',compact('worksheet','document','tracking','cancel'));
            }
            
        }
        else{
            $pdf = PDF::loadView('pdf.pdfview_ru',compact('worksheet','document','tracking','cancel'));
        }
        $pdf->save($folderPath.$file_name);      

        return $document;
    }
  
    
    /**
     *  Create signature image
     */
    public function setSignature(Request $request)
    {
        $folderPath = $this->checkDirectory('signatures');

        $img = $request->signed;
        $img = str_replace('data:image/png;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);
        $file_name = uniqid().".png";
        $success = file_put_contents($folderPath.$file_name, $data);
        
        if (!$request->type && !$request->form_screen) {
            if (!$request->document_id) $document = $this->createNewDocument($request,$file_name);
            else $document = $this->updateDocument($request,$file_name);
            $id = $document->id;
            $pdf_file = $this->savePdf($id);
            return back()->with('success', 'File '.$pdf_file.' signed and saved successfully')->with('new_document_id',$id);
        }
        elseif ($request->form_screen) {
            if (!$request->document_id) $document = $this->createNewDocument($request,$file_name);
            else $document = $this->updateDocument($request,$file_name);
            $id = $document->id;
            $pdf_file = $this->savePdfRu($id);
            return back()->with('success', 'File '.$pdf_file.' signed and saved successfully')->with('new_document_id',$id);
        }
        elseif ($request->cancel){
            if (!$request->document_id) $document = $this->createNewDocument($request,$file_name,true);
            else $document = $this->updateDocument($request,$file_name);
            $old_document = $this->cancelingDocument($document);
            $document_id = $document->id;            
            $pdf_file = $this->savePdfForCancel($document_id,$request->type);
            
            if (!$request->create_new) {
                return back()->with('success', 'File '.$pdf_file.' signed and saved successfully. Old document file '.$old_document->pdf_file)->with('new_document_id',$document_id);
            }
            else{
                $id = $request->id;
                $type = $request->type;
                return redirect()->route('formAfterCancel',compact('type','id','document_id'));               
            }
        }               
    }


    public function formAfterCancel($type, $id, $document_id)
    {
        $israel_cities = $this->israelCities();
        $worksheet = null;
        $request = (object)[];
        $request->quantity_sender = '1';
        $request->quantity_recipient = '1';
        if ($type === 'worksheet_id' || $type === 'draft_id') {

            if ($type === 'worksheet_id') $worksheet = NewWorksheet::find($id);
            elseif ($type === 'draft_id') $worksheet = CourierDraftWorksheet::find($id);

            $israel_cities['other'] = 'Другой город';
            $data_parcel = $this->fillResponseDataRu($worksheet, $request, true, true);
            $data_parcel = json_encode($data_parcel);
            
            return view('pdf.form_after_cancel',compact('israel_cities','data_parcel','document_id','type','id'));
        } 
        elseif ($type === 'eng_worksheet_id' || $type === 'eng_draft_id') {
            if ($type === 'eng_worksheet_id') $worksheet = PhilIndWorksheet::find($id);
            elseif ($type === 'eng_draft_id') $worksheet = CourierEngDraftWorksheet::find($id);

            $israel_cities['other'] = 'Other city';
            $data_parcel = $this->fillResponseDataEng($worksheet, $request, true, true);
            $data_parcel = json_encode($data_parcel);
            $domain = $this->getDomainRule();
            
            return view('pdf.form_after_cancel_eng',compact('israel_cities','data_parcel','document_id','type','id','domain'));
        }
    }


    public function formUpdateAfterCancel(Request $request)
    {
        $document = SignedDocument::find($request->document_id);
        $result = $document->updateWorksheet($request);
        
        if ($result) {

            switch ($request->type) {
                
                case "draft_id":

                $form_screen = $this->formToImg($request);
                return redirect()->route('getSignature')->with('draft_id', $result->id)->with('form_screen', $form_screen)->with('document_id', $request->document_id);

                break;

                case "eng_draft_id":

                return redirect()->route('getSignature')->with('eng_draft_id', $result->id)->with('document_id', $request->document_id);

                break;

                case "worksheet_id":

                $form_screen = $this->formToImg($request);
                return redirect()->route('getSignature')->with('worksheet_id', $result->id)->with('form_screen', $form_screen)->with('document_id', $request->document_id);

                break;

                case "eng_worksheet_id":

                return redirect()->route('getSignature')->with('eng_worksheet_id', $result->id)->with('document_id', $request->document_id);

                break;
            }                      
        }
        else{
            return redirect()->route('formAfterCancel')->with('status', 'Error update!');
        }
    }

    
    /**
     *  Create pdf file
     */
    public function pdfview($id)
    {
        $cancel = null;
        $document = SignedDocument::find($id);
        $worksheet = $document->getWorksheet();
        $tracking = $worksheet->tracking_main;
        //view()->share('items',$items);        
        return view('pdf.pdfview',compact('worksheet','document','tracking','cancel'));
    }


    /**
     *  Create pdf file
     */
    public function pdfviewForward($id)
    {
        $cancel = null;
        $document = SignedDocument::find($id);
        $worksheet = $document->getWorksheet();
        $tracking = $worksheet->tracking_main;      
        return view('pdf.pdfview_forward',compact('worksheet','document','tracking','cancel'));
    }


    /**
     *  Create pdf file
     */
    public function pdfviewRu($id)
    {
        $cancel = null;
        $document = SignedDocument::find($id);
        $worksheet = $document->getWorksheet();
        $tracking = $worksheet->tracking_main;      
        return view('pdf.pdfview_ru',compact('worksheet','document','tracking','cancel'));
    }

    
    public function savePdf($id)
    {
        $folderPath = $this->checkDirectory('documents');       
        $cancel = null;
        $document = SignedDocument::find($id);
        $worksheet = $document->getWorksheet();
        $tracking = $worksheet->tracking_main;
        $file_name = $document->uniq_id.'.pdf';
        $document->pdf_file = $file_name;
        $document->save();

        if ($this->getDomainRule() !== 'forward') {
            $pdf = PDF::loadView('pdf.pdfview',compact('worksheet','document','tracking','cancel'));
        }
        elseif($this->getDomainRule() === 'forward'){
            $pdf = PDF::loadView('pdf.pdfview_forward',compact('worksheet','document','tracking','cancel'));
        }
        $pdf->save($folderPath.$file_name);

        return $file_name;
    }


    public function savePdfRu($id)
    {
        $folderPath = $this->checkDirectory('documents');
        $cancel = null;
        $document = SignedDocument::find($id);
        $worksheet = $document->getWorksheet();
        $tracking = $worksheet->tracking_main;
        $file_name = $document->uniq_id.'.pdf';
        $document->pdf_file = $file_name;
        $document->save();

        $pdf = PDF::loadView('pdf.pdfview_ru',compact('worksheet','document','tracking','cancel'));
        $pdf->save($folderPath.$file_name);

        return $file_name;
    }


    public function savePdfForCancel($id,$type)
    {
        $folderPath = $this->checkDirectory('documents_for_cancel');
        $document = SignedDocument::find($id);
        $worksheet = $document->getWorksheet();
        $message = $this->messageForCancelPdf($type,$worksheet->id)[0];
        $old_document = SignedDocument::find($document->old_document_id);
        $file_name = 'for_cancel_'.$old_document->uniq_id.'.pdf';
        $document->file_for_cancel = $file_name;
        $document->save();        

        $pdf = PDF::loadView('pdf.pdf_for_cancel',compact('worksheet','document','message','type','old_document'));
        $pdf->save($folderPath.$file_name);

        return $file_name;
    }


    public function downloadPdf($id)
    {
        $document = SignedDocument::find($id);
        $worksheet = $document->getWorksheet();
        $tracking = $worksheet->tracking_main;
        $cancel = null;

        if (!$document->screen_ru_form) {
            if ($this->getDomainRule() !== 'forward') {
                $pdf = PDF::loadView('pdf.pdfview',compact('worksheet','document','tracking','cancel'));
            }
            elseif($this->getDomainRule() === 'forward'){
                $pdf = PDF::loadView('pdf.pdfview_forward',compact('worksheet','document','tracking','cancel'));
            }
        }
        else        
            $pdf = PDF::loadView('pdf.pdfview_ru',compact('worksheet','document','tracking','cancel'));
        
        return $pdf->download($document->uniq_id.'.pdf');
    }


    public function downloadAllPdf(Request $request)
    {
        switch ($request->type) {

            case "draft_id":

            $worksheet = CourierDraftWorksheet::find($request->id);

            break;

            case "eng_draft_id":

            $worksheet = CourierEngDraftWorksheet::find($request->id);

            break;

            case "worksheet_id":

            $worksheet = NewWorksheet::find($request->id);

            break;

            case "eng_worksheet_id":

            $worksheet = PhilIndWorksheet::find($request->id);

            break;
        }  

        $documents = $worksheet->signedDocuments;
        $last_doc = $worksheet->getLastDoc();
        $items = [];
        foreach ($documents as $document) {           
            if ($document->file_for_cancel) {
                $folderPath = $this->checkDirectory('documents_for_cancel');
                $file = $document->file_for_cancel;
                $items[] = ['path'=>$folderPath.$file, 'name'=>$file];
            }
            if ($last_doc->id != $document->id) 
                $folderPath = $this->checkDirectory('canceled_documents');
            else
                $folderPath = $this->checkDirectory('documents');
            $file = $document->pdf_file;
            $items[] = ['path'=>$folderPath.$file, 'name'=>$file];
        }

        return view('pdf.download_pdf',compact('items'));
    }


    public function downloadDirectory(Request $request)
    {
        return response()->download($request->path);
    }


    /**
     *  Create new document
     */
    public function createNewDocument($request,$file_name,$other = false)
    {
        if (!$other) {
            $new_document = new SignedDocument();
            $document = $new_document->createSignedDocument($request,$file_name);
            return $document;
        }
        else{
            $new_document = new SignedDocument();
            $document = $new_document->createSignedDocument($request,$file_name,$other);
            return $document;
        }
    }


    public function updateDocument($request,$file_name)
    {
        $document = SignedDocument::find($request->document_id);
        $document = $document->updateSignedDocument($request,$file_name);
        return $document;
    }


    public function createTempTable(Request $request)
    {
        if ($request->session_token) {
            Schema::create('table_'.$request->session_token, function (Blueprint $table) {
                $table->increments('id');
                $table->text('data')->nullable();
                $table->timestamps();
            });
        }  
        return $request->session_token;            
    }


    public function addToTempTable(Request $request)
    {       
        if ($request->get('session_token')) {
            $post = DB::table('table_'.$request->get('session_token'))->find(1);
            if (!$post) {
                DB::table('table_'.$request->get('session_token'))
                ->insert([
                    'data' => $request->getContent()
                ]);
            }
            else
                DB::table('table_'.$request->get('session_token'))
                ->where('id',1)
                ->update([
                    'data' => $request->getContent()
                ]);
        }
        return $request->getContent();
    }


    public function getFromTempTable($id)
    {
        if ($id) {
            $post = DB::table('table_'.$id)->find(1);
            if ($post) {
                return $post->data;
            }                        
        } 
        else return 'error';      
    }


    public function addSignedRuForm(Request $request)
    {
        $message = '';
        dd($request->session_token);
        // For signed forms
        /*if (!$request->parcels_qty) {
            $result = [];
            parse_str($request->getContent(),$result);        
            $result = (object)$result;
            $request = $result;
        }*/
        
        if (!$request->phone_exist_checked) {
            $message = $this->checkExistPhone($request,'courier_draft_worksheet');
            if ($message) {
                if ($request->signature) {
                    return redirect()->route('formWithSignature')->with('phone_exist', $message)->with('phone_number',$request->standard_phone);
                }
                else{
                    return redirect()->route('parcelForm')->with('phone_exist', $message)->with('phone_number',$request->standard_phone);
                }                 
            }
        }
        else{
            $message = $this->createRuParcel($request);
            if ($request->signature) {
                if ($message['id']) {
                    if ($request->session_token)
                        $this->deleteTempTable($request->session_token);
                    $form_screen = $this->formToImg($request);
                    return redirect()->route('getSignature')->with('draft_id', $message['id'])->with('form_screen', $form_screen);
                }
                else{
                    return redirect()->route('formWithSignature')->with('status', $message['message']);
                }
            }
            else{
                return redirect()->route('parcelForm')->with('status', $message['message']);
            }                      
        }        
        
        $message = $this->createRuParcel($request);

        if ($request->signature) {
            if ($message['id']) {
                    if ($request->session_token)
                        $this->deleteTempTable($request->session_token);
                    $form_screen = $this->formToImg($request);
                    return redirect()->route('getSignature')->with('draft_id', $message['id'])->with('form_screen', $form_screen);
                }
                else{
                    return redirect()->route('formWithSignature')->with('status', $message['message']);
                }
        }
        else{
            return redirect()->route('parcelForm')->with('status', $message['message']);
        }      
    }


    private function createRuParcel($request)
    {        
        $fields = Schema::getColumnListing('courier_draft_worksheet');
        $new_worksheet = new CourierDraftWorksheet();         

        foreach($fields as $field){

            if ($field === 'sender_name') {
                $new_worksheet->$field = $request->first_name.' '.$request->last_name;
            }
            else if($field === 'site_name'){
                $new_worksheet->$field = 'DD-C';
            }
            else if($field === 'recipient_name'){
                $new_worksheet->$field = $request->recipient_first_name.' '.$request->recipient_last_name;
            }
            else if($field === 'package_content'){
                $content = '';
                if ($request->clothing_quantity) {
                    $content .= 'Одежда: '.$request->clothing_quantity.'; ';
                }
                if ($request->shoes_quantity) {
                    $content .= 'Обувь: '.$request->shoes_quantity.'; ';
                }               
                if ($request->other_content_1) {
                    $content .= $request->other_content_1.': '.$request->other_quantity_1.'; ';
                }
                if ($request->other_content_2) {
                    $content .= $request->other_content_2.': '.$request->other_quantity_2.'; ';
                }
                if ($request->other_content_3) {
                    $content .= $request->other_content_3.': '.$request->other_quantity_3.'; ';
                }
                if ($request->other_content_4) {
                    $content .= $request->other_content_4.': '.$request->other_quantity_4.'; ';
                }
                if ($request->other_content_5) {
                    $content .= $request->other_content_5.': '.$request->other_quantity_5.'; ';
                }
                if ($request->other_content_6) {
                    $content .= $request->other_content_6.': '.$request->other_quantity_6.'; ';
                }
                if ($request->other_content_7) {
                    $content .= $request->other_content_7.': '.$request->other_quantity_7.'; ';
                }
                if ($request->other_content_8) {
                    $content .= $request->other_content_8.': '.$request->other_quantity_8.'; ';
                }
                if ($request->other_content_9) {
                    $content .= $request->other_content_9.': '.$request->other_quantity_9.'; ';
                }
                if ($request->other_content_10) {
                    $content .= $request->other_content_10.': '.$request->other_quantity_10.'; ';
                }
                if(!$content){
                    $content = 'Пусто: 0';
                }                

                $new_worksheet->$field = trim($content);
            }
            else if ($field === 'comment_2'){
                if ($request->need_box) $new_worksheet->$field = $request->need_box;
                if ($request->comment_2) $new_worksheet->$field = $request->comment_2;
            }
            else if ($field !== 'created_at'){
                $new_worksheet->$field = $request->$field;
            }           
        }

        $new_worksheet->in_trash = false;
        if (in_array($new_worksheet->sender_city, array_keys($this->israel_cities))) {
            $new_worksheet->shipper_region = $this->israel_cities[$new_worksheet->sender_city];
        }        

        // New parcel form
        if (null !== $request->status_box) {
            if ($request->status_box === 'false') {
                $new_worksheet->status = 'Забрать';
            } 
            else{
                $new_worksheet->status = 'Коробка';
            }
        }
         
        if (null !== $request->need_box) {
            if ($request->need_box === 'Мне не нужна коробка') {
                $new_worksheet->status = 'Забрать';
            }
            else{
                $new_worksheet->status = 'Коробка';
            }
        }        

        $new_worksheet->date = date('Y-m-d');
        $new_worksheet->status_date = date('Y-m-d'); 
        $new_worksheet->order_date = date('Y-m-d');      

        if ($new_worksheet->save()){           

            $this->addingOrderNumber($new_worksheet->standard_phone, 'ru');
            $work_sheet_id = $new_worksheet->id;       
            $message = ['message'=>'Заказ посылки успешно создан !','id'=>$work_sheet_id];
            $new_worksheet = CourierDraftWorksheet::find($work_sheet_id);
            $new_worksheet->checkCourierTask($new_worksheet->status);

            // Packing
            $fields_packing = ['payer', 'contract', 'type', 'track_code', 'full_shipper', 'full_consignee', 'country_code', 'postcode', 'region', 'district', 'city', 'street', 'house', 'body', 'room', 'phone', 'tariff', 'tariff_cent', 'weight_kg', 'weight_g', 'service_code', 'amount_1', 'amount_2', 'attachment_number', 'attachment_name', 'amount_3', 'weight_enclosures_kg', 'weight_enclosures_g', 'value_euro', 'value_cent', 'work_sheet_id'];
            $j=1;
            $paking_not_create = true;

            for ($i=1; $i < 11; $i++) { 
                if ($request->other_content_.$i) {
                    $packing_sea = new PackingSea();
                    foreach($fields_packing as $field){
                        if ($field === 'type') {
                            $packing_sea->$field = $request->tariff;
                        }
                        else if ($field === 'full_shipper') {
                            $packing_sea->$field = $request->first_name.' '.$request->last_name;
                        }
                        else if ($field === 'full_consignee') {
                            $packing_sea->$field = $request->recipient_first_name.' '.$request->recipient_last_name;
                        }
                        else if ($field === 'country_code') {
                            $packing_sea->$field = $request->recipient_country;
                        }
                        else if ($field === 'postcode') {
                            $packing_sea->$field = $request->recipient_postcode;
                        }
                        else if ($field === 'city') {
                            $packing_sea->$field = $request->recipient_city;
                        }
                        else if ($field === 'street') {
                            $packing_sea->$field = $request->recipient_street;
                        }
                        else if ($field === 'house') {
                            $packing_sea->$field = $request->recipient_house;
                        }
                        else if ($field === 'room') {
                            $packing_sea->$field = $request->recipient_room;
                        }
                        else if ($field === 'phone') {
                            $packing_sea->$field = $request->recipient_phone;
                        }
                        else if ($field === 'tariff') {
                            $packing_sea->$field = null;
                        }
                        else if ($field === 'work_sheet_id') {
                            $packing_sea->$field = $work_sheet_id;
                        }
                        else if ($field === 'attachment_number') {
                            $packing_sea->$field = $j;
                        }
                        else if ($field === 'attachment_name') {
                            $packing_sea->$field = $request->other_content_.$i;
                        }
                        else if ($field === 'amount_3') {
                            $packing_sea->$field = $request->other_quantity_.$i;
                        }
                        else{
                            $packing_sea->$field = $request->$field;
                        }
                    }
                    $j++;
                    if ($packing_sea->save()) {
                        $paking_not_create = false;
                    }
                }
            } 

            if ($paking_not_create) {
                $packing_sea = new PackingSea();
                foreach($fields_packing as $field){
                    if ($field === 'type') {
                        $packing_sea->$field = $request->tariff;
                    }
                    else if ($field === 'full_shipper') {
                        $packing_sea->$field = $request->first_name.' '.$request->last_name;
                    }
                    else if ($field === 'full_consignee') {
                        $packing_sea->$field = $request->recipient_first_name.' '.$request->recipient_last_name;
                    }
                    else if ($field === 'country_code') {
                        $packing_sea->$field = $request->recipient_country;
                    }
                    else if ($field === 'postcode') {
                        $packing_sea->$field = $request->recipient_postcode;
                    }
                    else if ($field === 'city') {
                        $packing_sea->$field = $request->recipient_city;
                    }
                    else if ($field === 'street') {
                        $packing_sea->$field = $request->recipient_street;
                    }
                    else if ($field === 'house') {
                        $packing_sea->$field = $request->recipient_house;
                    }
                    else if ($field === 'room') {
                        $packing_sea->$field = $request->recipient_room;
                    }
                    else if ($field === 'phone') {
                        $packing_sea->$field = $request->recipient_phone;
                    }
                    else if ($field === 'tariff') {
                        $packing_sea->$field = null;
                    }
                    else if ($field === 'work_sheet_id') {
                        $packing_sea->$field = $work_sheet_id;
                    }
                    else if ($field === 'attachment_number') {
                        $packing_sea->$field = 1;
                    }
                    else if ($field === 'attachment_name') {
                        $packing_sea->$field = 'Пусто';
                    }
                    else if ($field === 'amount_3') {
                        $packing_sea->$field = '0';
                    }
                    else{
                        $packing_sea->$field = $request->$field;
                    }
                }

                $packing_sea->save();
            }
        }
        else{
            $message = ['message'=>'Ошибка сохранения !','id'=>''];
        }             
        
        return $message;        
    }


    /*public function deleteTempTable($session_token)
    {
        if ($session_token) {
            Schema::dropIfExists('table_'.$session_token);
        }       
    }*/
}
