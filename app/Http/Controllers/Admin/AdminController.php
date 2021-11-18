<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NewWorksheet;
use App\PhilIndWorksheet;
use App\PackingEngNew;
use App\CourierDraftWorksheet;
use App\CourierEngDraftWorksheet;
use App\Receipt;
use App\Exports\ReceiptExport;
use App\ReceiptArchive;
use DB;
use Excel;

class AdminController extends Controller
{
	const ROLES_ARR = array('admin' => 'admin', 'user' => 'user', 'warehouse' => 'warehouse', 'office_1' => 'office_1','office_ru' => 'office_ru', 'office_agent_ru' => 'office_agent_ru', 'viewer' => 'viewer', 'china_admin' => 'china_admin', 'china_viewer' => 'china_viewer', 'office_eng' => 'office_eng', 'office_ind' => 'office_ind', 'viewer_eng' => 'viewer_eng', 'viewer_1' => 'viewer_1', 'viewer_2' => 'viewer_2', 'viewer_3' => 'viewer_3', 'viewer_4' => 'viewer_4', 'viewer_5' => 'viewer_5');
	const VIEWER_ARR = array('viewer_1', 'viewer_2', 'viewer_3', 'viewer_4', 'viewer_5');
	private $ru_status_arr = ["Возврат", "Коробка", "Забрать", "Уточнить", "Думают", "Отмена", "Подготовка"];
	private $en_status_arr = ["Pending", "Return", "Box", "Pick up", "Specify", "Think", "Canceled"];


    protected function checkRowColor(Request $request)
    {
        $which_admin = $request->input('which_admin');
        $row_arr = $request->input('row_id');
        $old_color_arr = $request->input('old_color');

        for ($i=0; $i < count($row_arr); $i++) { 
            if ($which_admin === 'ru') {
            	if ($old_color_arr[$i] === 'tr-orange') {
                	
                	$worksheet = NewWorksheet::find($row_arr[$i]);             
                    $error_message = 'Заполните обязателные поля в строке с телефоном отправителя '.$worksheet->standard_phone.': ';

                    if (!$worksheet->sender_name) $error_message .= 'Отправитель,';
                    if (!$worksheet->standard_phone) $error_message .= 'Телефон (стандарт),';
                    if (!$worksheet->recipient_name) $error_message .= 'Получатель,';
                    if (!$worksheet->recipient_city) $error_message .= 'Город получателя,';
                    if (!$worksheet->recipient_street) $error_message .= 'Улица получателя,';
                    if (!$worksheet->recipient_house) $error_message .= '№ дома пол-ля,';
                    if (!$worksheet->recipient_room) $error_message .= '№ кв. пол-ля,';
                    if (!$worksheet->recipient_phone) $error_message .= 'Телефон получателя,';

                    if ($error_message !== 'Заполните обязателные поля в строке с телефоном отправителя '.$worksheet->standard_phone.': ') {
                    	return response()->json(['error' => $error_message]);
                    }
                }             
            }
            elseif ($which_admin === 'en') {
            	if ($old_color_arr[$i] === 'tr-orange') {
            		
            		$worksheet = PhilIndWorksheet::find($row_arr[$i]);
            		$packing = PackingEngNew::where('work_sheet_id',$row_arr[$i])->first();
            		$country = '';
            		$error_message = 'Fill in required fields with shipper phone '.$worksheet->standard_phone.': ';

            		if ($packing) $country = $packing->country;
            		if (!$country) {
            			$tracking = $worksheet->tracking_main;
            			if (stripos($tracking, 'IN') !== false) $country = 'India';
            			if (stripos($tracking, 'NE') !== false) $country = 'Nepal';
            		}

            		if ($country && $country === 'India') {
            			if (!$worksheet->shipper_name) $error_message .= 'Shipper\'s name,';
            			if (!$worksheet->shipper_address) $error_message .= 'Shipper\'s address,';
            			if (!$worksheet->standard_phone) $error_message .= 'Shipper\'s phone (standard),';
            			if (!$worksheet->consignee_name) $error_message .= 'Consignee\'s name,';
            			if (!$worksheet->consignee_address) $error_message .= 'Consignee\'s address,';
            			if (!$worksheet->consignee_phone) $error_message .= 'Consignee\'s phone number,';
            			if (!$worksheet->house_name) $error_message .= 'House name,';
            			if (!$worksheet->state_pincode) $error_message .= 'State pincode,';
            			if (!$worksheet->post_office) $error_message .= 'Local post office,';
            			if (!$worksheet->district) $error_message .= 'District/City,';

            			if ($error_message !== 'Fill in required fields with shipper phone '.$worksheet->standard_phone.': ') {
            				return response()->json(['error' => $error_message]);
            			}			
            		}
            		elseif ($country && $country !== 'India') {
            			if (!$worksheet->shipper_name) $error_message .= 'Shipper\'s name,';
            			if (!$worksheet->shipper_address) $error_message .= 'Shipper\'s address,';
            			if (!$worksheet->standard_phone) $error_message .= 'Shipper\'s phone (standard),';
            			if (!$worksheet->consignee_name) $error_message .= 'Consignee\'s name,';
            			if (!$worksheet->consignee_address) $error_message .= 'Consignee\'s address,';
            			if (!$worksheet->consignee_phone) $error_message .= 'Consignee\'s phone number,';

            			if ($error_message !== 'Fill in required fields with shipper phone '.$worksheet->standard_phone.': ') {
            				return response()->json(['error' => $error_message]);
            			}
            		}
            	}           	
            }
        }
        
        return response()->json(['success' => 'success']);
    }
	
	
	protected function checkStatus($which_admin, $id, $status)
	{
		if ($which_admin === 'ru') {
			$worksheet = NewWorksheet::find($id);
			if (!$worksheet->tracking_main && !in_array($status, $this->ru_status_arr)) {
				return 'STATUS ERROR!';
			}
			else return '';
		}
		elseif ($which_admin === 'en') {
			$worksheet = PhilIndWorksheet::find($id);
			if (!$worksheet->tracking_main && !in_array($status, $this->en_status_arr)) {
				return 'STATUS ERROR!';
			}
			else return '';
		}	
	}
	
	
	protected function new_columns(){

		$arr_columns = [];
		$new_column_1 = '';
        $new_column_2 = '';
        $new_column_3 = '';
        $new_column_4 = '';
        $new_column_5 = '';

		if (Schema::hasColumn('new_worksheet', 'new_column_1'))
		{
			$new_column_1 = 'Дополнительная колонка 1';
		}
		$arr_columns[] = $new_column_1;
		
		if (Schema::hasColumn('new_worksheet', 'new_column_2'))
		{
			$new_column_2 = 'Дополнительная колонка 2';
		}
		$arr_columns[] = $new_column_2;
		
		if (Schema::hasColumn('new_worksheet', 'new_column_3'))
		{
			$new_column_3 = 'Дополнительная колонка 3';
		}
		$arr_columns[] = $new_column_3;
		
		if (Schema::hasColumn('new_worksheet', 'new_column_4'))
		{
			$new_column_4 = 'Дополнительная колонка 4';
		}
		$arr_columns[] = $new_column_4;
		
		if (Schema::hasColumn('new_worksheet', 'new_column_5'))
		{
			$new_column_5 = 'Дополнительная колонка 5';
		}
		$arr_columns[] = $new_column_5;

		return $arr_columns;
	}


	protected function new_china_columns(){

		$arr_columns = [];
		$new_column_1 = '';
        $new_column_2 = '';
        $new_column_3 = '';
        $new_column_4 = '';
        $new_column_5 = '';

		if (Schema::hasColumn('china_worksheet', 'new_column_1'))
		{
			$new_column_1 = 'Additional column 1';
		}
		$arr_columns[] = $new_column_1;
		
		if (Schema::hasColumn('china_worksheet', 'new_column_2'))
		{
			$new_column_2 = 'Additional column 2';
		}
		$arr_columns[] = $new_column_2;
		
		if (Schema::hasColumn('china_worksheet', 'new_column_3'))
		{
			$new_column_3 = 'Additional column 3';
		}
		$arr_columns[] = $new_column_3;
		
		if (Schema::hasColumn('china_worksheet', 'new_column_4'))
		{
			$new_column_4 = 'Additional column 4';
		}
		$arr_columns[] = $new_column_4;
		
		if (Schema::hasColumn('china_worksheet', 'new_column_5'))
		{
			$new_column_5 = 'Additional column 5';
		}
		$arr_columns[] = $new_column_5;

		return $arr_columns;
	}


	protected function new_phil_ind_columns(){

		$arr_columns = [];
		$new_column_1 = '';
        $new_column_2 = '';
        $new_column_3 = '';
        $new_column_4 = '';
        $new_column_5 = '';

		if (Schema::hasColumn('phil_ind_worksheet', 'new_column_1'))
		{
			$new_column_1 = 'Additional column 1';
		}
		$arr_columns[] = $new_column_1;
		
		if (Schema::hasColumn('phil_ind_worksheet', 'new_column_2'))
		{
			$new_column_2 = 'Additional column 2';
		}
		$arr_columns[] = $new_column_2;
		
		if (Schema::hasColumn('phil_ind_worksheet', 'new_column_3'))
		{
			$new_column_3 = 'Additional column 3';
		}
		$arr_columns[] = $new_column_3;
		
		if (Schema::hasColumn('phil_ind_worksheet', 'new_column_4'))
		{
			$new_column_4 = 'Additional column 4';
		}
		$arr_columns[] = $new_column_4;
		
		if (Schema::hasColumn('phil_ind_worksheet', 'new_column_5'))
		{
			$new_column_5 = 'Additional column 5';
		}
		$arr_columns[] = $new_column_5;

		return $arr_columns;
	}


	protected function translit($s) {
		$s = (string) $s; // преобразуем в строковое значение
		$s = strip_tags($s); // убираем HTML-теги
		$s = str_replace(array("\n", "\r"), " ", $s); // убираем перевод каретки
		$s = preg_replace("/\s+/", ' ', $s); // удаляем повторяющие пробелы
		$s = trim($s); // убираем пробелы в начале и конце строки
		//$s = function_exists('mb_strtolower') ? mb_strtolower($s) : strtolower($s); // переводим строку в нижний регистр (иногда надо задать локаль)
		
		$s = strtr($s, array('а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'j','з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'shch','ы'=>'y','э'=>'e','ю'=>'yu','я'=>'ya','ъ'=>'','ь'=>'','А'=>'A','Б'=>'B','В'=>'V','Г'=>'G','Д'=>'D','Е'=>'E','Ё'=>'E','Ж'=>'J','З'=>'Z','И'=>'I','Й'=>'Y','К'=>'K','Л'=>'L','М'=>'M','Н'=>'N','О'=>'O','П'=>'P','Р'=>'R','С'=>'S','Т'=>'T','У'=>'U','Ф'=>'F','Х'=>'H','Ц'=>'C','Ч'=>'Ch','Ш'=>'Sh','Щ'=>'Shch','Ы'=>'Y','Э'=>'E','Ю'=>'Yu','Я'=>'Ya','Ь'=>'','Ъ'=>''));
		
		$s = preg_replace("/[^0-9a-z-_ ]/i", "", $s); // очищаем строку от недопустимых символов
		//$s = str_replace(" ", "-", $s); // заменяем пробелы знаком минус
		
		return $s; // возвращаем результат
	}


	public function adminReceipts($legal_entity)
	{        
        if ($legal_entity === 'dd') {
        	$receipts_obj = Receipt::where('legal_entity','Д.Дымщиц')->orderByRaw('CONVERT(SUBSTRING(receipt_number, 3), SIGNED)')->paginate(10);
        	$title = 'Квитанции ДД (Receipts DD)';
        }
        elseif ($legal_entity === 'ul') {
        	$receipts_obj = Receipt::where('legal_entity','Юнион Логистик')->orderByRaw('CONVERT(receipt_number, SIGNED)')->paginate(10);
        	$title = 'Квитанции ЮЛ (Receipts UL)';
        }
        else{
        	$receipts_obj = null;
        	$title = '';
        }            
        
        return view('admin.receipts.receipts', compact('title','receipts_obj','legal_entity'));
    }


    public function adminReceiptsArchive()
	{
        $title = 'Notifications';
        $check_archive = ReceiptArchive::where([
        	['status',false],
        	['update_date',date('Y-m-d')]
        ])->first();

        if ($check_archive) {
        	ReceiptArchive::where([
        		['status',false],
        		['update_date',date('Y-m-d')]
        	])->update([
        		'status' => true
        	]);
        }

        $archive_obj = ReceiptArchive::where('status',true)->paginate(10);     
        
        return view('admin.receipts.receipts_archive', compact('title','archive_obj'));
    }


    public function receiptsArchiveShow($id)
	{
		$receipt = ReceiptArchive::find($id);
		$title = 'Изменение строки (Update row) '.$receipt->id;

		return view('admin.receipts.receipts_archive_update', compact('title','receipt'));
	}

    
    public function receiptsArchiveUpdate(Request $request, $id)
    {
    	ReceiptArchive::where('id', $id)->update([
    		'comment' => $request->input('comment')
    	]);
    	return redirect()->to(session('this_previous_url'))->with('status', 'Строка успешно обновлена (Row updated successfully)!');
    }


    public function receiptsDouble($id)
    {
    	$receipt = Receipt::find($id);
    	$data = [
    		'receipt_number' => $receipt->receipt_number,
			'legal_entity' => $receipt->legal_entity,
			'courier_name' => $receipt->courier_name,
			'double' => 1
    	];
    	Receipt::insert($data);

    	return redirect()->to(session('this_previous_url'))->with('status', 'Дубль успешно добавлен (Double added successfully)!');
    }


	public function receiptsShow($id)
	{
		$receipt = Receipt::find($id);
		$title = 'Изменение строки (Update row) '.$receipt->id;

		return view('admin.receipts.receipts_update', compact('title','receipt'));
	}


	public function receiptsUpdate(Request $request, $id)
	{
		$receipt = Receipt::find($id);		
		$data = $request->all();
		$fields = $this->getTableColumns('receipts');
		$number = $request->input('receipt_number');
		$range = $request->input('range_number');		
		
		$message = 'Строка успешно обновлена (Row updated successfully)!';

		if (!$data['double']) {
			if (!$data['tracking_main'] || !$data['sum'] || !$data['date']){
				$message = 'Нельзя сохранить строку с пустыми: Номер посылки, Сумма, Дата (You cannot save a line with empty ones: Tracking number, Amount, Date)!';
				return redirect()->to(session('this_previous_url'))->with('status-error', $message);
			}								

			if ($data['tracking_main']) {
				if (!$this->trackingValidate($data['tracking_main'])) return redirect()->to(session('this_previous_url'))->with('status-error', 'Tracking number is not correct.');
				
				if ($receipt->tracking_main) {
					ReceiptArchive::where('tracking_main', $receipt->tracking_main)->delete();
				}
				
				$string = $this->checkSkipped($range, $id);
				if($string) $message .= $string;

				$string = $this->addReceiptRow($data, $id, $number, false);
				if($string) $message .= $string;
			}
		}
		else{
			if (!$data['tracking_main']){
				$message = 'Нельзя сохранить строку с пустым: Номер посылки (You cannot save a line with empty one: Tracking number)!';
				return redirect()->to(session('this_previous_url'))->with('status-error', $message);
			}

			if (!$this->trackingValidate($data['tracking_main'])) return redirect()->to(session('this_previous_url'))->with('status-error', 'Tracking number is not correct.');

			$origin = Receipt::where([
				['receipt_number',$number],
				['double',0]
			])->first();

			if (!$origin->tracking_main){
				$message = 'Нельзя сохранить дубль с пустым оригиналом (Can\'t save take with blank original)!';
				return redirect()->to(session('this_previous_url'))->with('status-error', $message);
			}

			if ($receipt->tracking_main) {
				ReceiptArchive::where('tracking_main', $receipt->tracking_main)->delete();
			}
			
			$string = $this->addReceiptRow($data, $id, $number, true);
			if($string) $message .= $string;
		}		
		
		foreach($fields as $field){						
			if ($field !== 'created_at') {
				$receipt->$field = $request->input($field);
			}
		}

		$receipt->save();

		$last_id = ReceiptArchive::where([
			['receipt_id',$id],
			['worksheet_id',null]])
		->get()->last();

		if ($last_id) {
			$last_id = $last_id->id;
			ReceiptArchive::where([
				['receipt_id',$id],
				['worksheet_id',null],
				['tracking_main','<>',null],
				['id','<>',$last_id]
			])->delete();
		}		

		ReceiptArchive::where([
				['receipt_id',$id],
				['worksheet_id',null],
				['which_admin',null]
			])->delete();
		return redirect()->to(session('this_previous_url'))->with('status', $message);	
	}


	private function checkSkipped($range, $id)
	{
		$message = '';
		$skipped = '';
		$range_arr = Receipt::where([
			['range_number',$range],
			['id','<',(int)$id],
			['tracking_main',null]
		])->get();

		$archive = [];
		if ($range_arr->count()) {				
			foreach ($range_arr as $value) {
				$result = ReceiptArchive::where('receipt_number',$value->receipt_number)->first();
				if (!$result) {
					$archive[] = [
						'receipt_id' => $value->id,
						'receipt_number' => $value->receipt_number,
						'description' => 'Пропущена запись по квитанции (Skipped recording on receipt): '.$value->receipt_number
					];
					$skipped .= $value->receipt_number.',';
				}				
			}

			if ($archive) {
				ReceiptArchive::insert($archive);
				$skipped = substr($skipped,0,-1);
				$message = 'Пропущена запись по квитанции (Skipped recording on receipt): '.$skipped.' !';
			}			
		}

		return $message;
	}


	private function addReceiptRow($data, $id, $number, $double)
	{
		$message = '';
		$archive = [];

		// If double
		if ($double) {
			$origin = Receipt::where([
				['receipt_number',$number],
				['double',0]
			])->first();

			$data['date'] = $origin->date;
			$data['sum'] = $origin->sum;
		}		

		// If not double
		$pos = strripos($data['tracking_main'], 'CD');
		if ($pos === false) {
			$worksheet = PhilIndWorksheet::where('tracking_main', $data['tracking_main'])->first();
			$courier_worksheet = CourierEngDraftWorksheet::where('tracking_main', $data['tracking_main'])->first();
			if ($worksheet) {
				$worksheet->payment_date_comments = $data['date'];
				$worksheet->amount_payment = $data['sum'];
				$worksheet->save();	

				ReceiptArchive::where('tracking_main', $data['tracking_main'])->delete();					
			}
			elseif ($courier_worksheet) {
				ReceiptArchive::where('tracking_main', $data['tracking_main'])->delete();
			}
			else{
				$notification = ReceiptArchive::where('tracking_main', $data['tracking_main'])->first();
				if (!$notification) {
					$message = $this->checkReceipt(null, $id, 'en', $data['tracking_main'], $number);
				}				
			}
		}
		else{
			$worksheet = NewWorksheet::where('tracking_main', $data['tracking_main'])->first();
			$courier_worksheet = CourierDraftWorksheet::where('tracking_main', $data['tracking_main'])->first();
			if ($worksheet) {
				$worksheet->pay_date = $data['date'];
				$worksheet->pay_sum = $data['sum'];
				$worksheet->save();	

				ReceiptArchive::where('tracking_main', $data['tracking_main'])->delete();					
			}
			elseif ($courier_worksheet) {
				ReceiptArchive::where('tracking_main', $data['tracking_main'])->delete();
			}
			else{
				$notification = ReceiptArchive::where('tracking_main', $data['tracking_main'])->first();
				if (!$notification) {
					$message = $this->checkReceipt(null, $id, 'ru', $data['tracking_main'], $number);
				}				
			}
		}

		return $message;
	}


	public function deleteReceipt(Request $request)
	{
		$id = $request->input('action');
		$receipt = Receipt::find($id);

		Receipt::where('id', $id)->delete();
		ReceiptArchive::where('tracking_main', $receipt->tracking_main)->delete();
		
		return redirect()->to(session('this_previous_url'))->with('status', 'Строка успешно удалена (Row deleted successfully)!');
	}


	public function deleteReceipts(Request $request)
	{
		$start = $request->input('range_start');

		if ($start) {
			if ($request->input('range_select') === 'ХХ01') {

				if ($request->input('legal_entity') === 'DD') {
					Receipt::where([
						['receipt_number', '>=' ,'DD'.$start.'01'],
						['receipt_number', '<=' ,'DD'.$start.'50'],
					])->delete();
					ReceiptArchive::where([
						['receipt_number', '>=' ,'DD'.$start.'01'],
						['receipt_number', '<=' ,'DD'.$start.'50'],
					])->delete();
				}
				else{
					Receipt::where([
						['receipt_number', '>=' ,$start.'01'],
						['receipt_number', '<=' ,$start.'50'],
					])->delete();
					ReceiptArchive::where([
						['receipt_number', '>=' ,$start.'01'],
						['receipt_number', '<=' ,$start.'50'],
					])->delete();
				}

			}
			else{
				$last_symbol = ((int)substr($start, -1) != 9)?substr($start,0,-1).((int)substr($start, -1)+1):substr($start,0,-2).((int)substr($start, -2)+1);
				if ($request->input('legal_entity') === 'DD') {
					Receipt::where([
						['receipt_number', '>=' ,'DD'.$start.'51'],
						['receipt_number', '<=' ,'DD'.$last_symbol.'00'],
					])->delete();
					ReceiptArchive::where([
						['receipt_number', '>=' ,'DD'.$start.'51'],
						['receipt_number', '<=' ,'DD'.$last_symbol.'00'],
					])->delete();
				}
				else{
					Receipt::where([
						['receipt_number', '>=' ,$start.'51'],
						['receipt_number', '<=' ,$last_symbol.'00'],
					])->delete();
					ReceiptArchive::where([
						['receipt_number', '>=' ,$start.'51'],
						['receipt_number', '<=' ,$last_symbol.'00'],
					])->delete();
				}
			}
			return redirect()->to(session('this_previous_url'))->with('status', 'Строки успешно удалены (Rows deleted successfully)!');
		}
		return redirect()->to(session('this_previous_url'))->with('status-error', 'Введите начало диапазона (Enter the start of the range)!');		
	}


	public function receiptsAdd(Request $request)
	{
		$data = [];
		$start = $request->input('range_start');
		$courier = $request->input('courier_name');
		
		if ($start) {
			if ($request->input('range_select') === 'ХХ01') {
				for ($i=1; $i <= 50; $i++) { 
					if ($request->input('legal_entity') === 'DD') {
						$data[] = [
							'receipt_number' => 'DD'.$start.(($i<10)?'0'.$i:$i),
							'range_number' => 'DD'.$start,
							'courier_name' => $courier,
							'legal_entity' => 'Д.Дымщиц'
						];
					}
					else{
						$data[] = [
							'receipt_number' => $start.(($i<10)?'0'.$i:$i),
							'range_number' => $start,
							'courier_name' => $courier,
							'legal_entity' => 'Юнион Логистик'
						];
					}
				}
			}
			else{
				$last_symbol = ((int)substr($start, -1) != 9)?substr($start,0,-1).((int)substr($start, -1)+1):substr($start,0,-2).((int)substr($start, -2)+1);
				for ($i=51; $i <= 100; $i++) { 
					if ($request->input('legal_entity') === 'DD') {
						$data[] = [
							'receipt_number' => ($i==100)?('DD'.$last_symbol.'00'):('DD'.$start.$i),
							'range_number' => 'DD'.$start,
							'courier_name' => $courier,
							'legal_entity' => 'Д.Дымщиц'
						];
					}
					else{
						$data[] = [
							'receipt_number' => ($i==100)?($last_symbol.'00'):($start.$i),
							'range_number' => $start,
							'courier_name' => $courier,
							'legal_entity' => 'Юнион Логистик'
						];
					}
				}
			}

			Receipt::insert($data);
			return redirect()->to(session('this_previous_url'))->with('status', 'Строки успешно добавлены (Rows added successfully)!');
		}
		
		return redirect()->to(session('this_previous_url'))->with('status-error', 'Введите начало диапазона (Enter the start of the range)!');
	}


	public function receiptsFilter(Request $request, $legal_entity)
	{
		$search = $request->table_filter_value;
		$filter_arr = [];
		$attributes = Receipt::first()->attributesToArray();
		$receipts_obj = null;
		$title = '';

        $id_arr = [];
        $new_arr = [];      

        if ($request->table_columns) {
        	if ($legal_entity === 'dd') {
        		$receipts_obj = Receipt::where([
        			[$request->table_columns, 'like', '%'.$search.'%'],
        			['legal_entity','Д.Дымщиц']
        		])->orderByRaw('CONVERT(SUBSTRING(receipt_number, 3), SIGNED)')->paginate(10);
        		$title = 'Фильтр Квитанций ДД (Receipts Filter DD)';
        	}
        	elseif ($legal_entity === 'ul') {
        		$receipts_obj = Receipt::where([
        			[$request->table_columns, 'like', '%'.$search.'%'],
        			['legal_entity','Юнион Логистик']
        		])->orderByRaw('CONVERT(receipt_number, SIGNED)')->paginate(10);
        		$title = 'Фильтр Квитанций ЮЛ (Receipts Filter UL)';
        	}      	
        }
        else{
        	foreach($attributes as $key => $value)
        	{
        		if ($key !== 'created_at' && $key !== 'updated_at') {
        			if ($legal_entity === 'dd'){
        				$sheet = Receipt::where([
        					[$key, 'like', '%'.$search.'%'],
        					['legal_entity','Д.Дымщиц']
        				])->get()->first();
        				if ($sheet) {       				
        					$temp_arr = Receipt::where([
        						[$key, 'like', '%'.$search.'%'],
        						['legal_entity','Д.Дымщиц']
        					])->orderByRaw('CONVERT(SUBSTRING(receipt_number, 3), SIGNED)')->get();
        					$new_arr = $temp_arr->filter(function ($item, $k) use($id_arr) {
        						if (!in_array($item->id, $id_arr)) { 
        							$id_arr[] = $item->id;       						  
        							return $item;    					
        						}       					       					
        					});        				
        					$filter_arr[] = $new_arr;   				         		
        				}
        			}
        			elseif ($legal_entity === 'ul'){
        				$sheet = Receipt::where([
        					[$key, 'like', '%'.$search.'%'],
        					['legal_entity','Юнион Логистик']
        				])->get()->first();
        				if ($sheet) {       				
        					$temp_arr = Receipt::where([
        						[$key, 'like', '%'.$search.'%'],
        						['legal_entity','Юнион Логистик']
        					])->orderByRaw('CONVERT(receipt_number, SIGNED)')->get();
        					$new_arr = $temp_arr->filter(function ($item, $k) use($id_arr) {
        						if (!in_array($item->id, $id_arr)) { 
        							$id_arr[] = $item->id;       						  
        							return $item;    					
        						}       					       					
        					});        				
        					$filter_arr[] = $new_arr;   				         		
        				}
        			}
        		}         		
        	}

        	return view('admin.receipts.receipts_find', compact('title','filter_arr','legal_entity'));      	
        }
        
        $data = $request->all();             
        
        return view('admin.receipts.receipts', compact('title','receipts_obj','legal_entity','data'));
    }


    public function exportExcelReceipts()
	{

    	return Excel::download(new ReceiptExport, 'ReceiptExport.xlsx');

	}


	public function receiptsArchiveFilter(Request $request)
	{
        $title = 'Notifications Filter';
        $search = $request->table_filter_value;
        $filter_arr = [];
        $attributes = ReceiptArchive::first()->attributesToArray();

        $id_arr = [];
        $new_arr = [];      

        if ($request->table_columns) {
        	$archive_obj = ReceiptArchive::where($request->table_columns, 'like', '%'.$search.'%')->paginate(10);
        }
        else{
        	foreach($attributes as $key => $value)
        	{
        		if ($key !== 'created_at' && $key !== 'updated_at') {
        			$sheet = ReceiptArchive::where($key, 'like', '%'.$search.'%')->get()->first();
        			if ($sheet) {       				
        				$temp_arr = ReceiptArchive::where($key, 'like', '%'.$search.'%')->get();
        				$new_arr = $temp_arr->filter(function ($item, $k) use($id_arr) {
        					if (!in_array($item->id, $id_arr)) { 
        						$id_arr[] = $item->id;       						  
        						return $item;    					
        					}       					       					
        				});        				
        				$filter_arr[] = $new_arr;   				         		
        			}
        		}         		
        	}

        	return view('admin.receipts.receipts_archive_find', compact('title','filter_arr'));      	
        }
        
        $data = $request->all();             
        
        return view('admin.receipts.receipts_archive', compact('title','archive_obj','data'));
    }


    public function receiptsSum(Request $request, $legal_entity){
    	$from_date = $request->input('from_date');
    	$to_date = $request->input('to_date');
    	$sum = 0;

    	if ((int)$from_date > (int)$to_date) {
    		return json_encode(['error'=>'Начальная дата больше конечной']);
    	}
    	else{
    		if ($legal_entity === 'dd') {
    			$sum = Receipt::where([
    				['legal_entity','Д.Дымщиц'],
    				['date','>=',$from_date],
    				['date','<=',$to_date]
    			])->sum('sum');
    		}
    		elseif ($legal_entity === 'ul') {
    			$sum = Receipt::where([
    				['legal_entity','Юнион Логистик'],
    				['date','>=',$from_date],
    				['date','<=',$to_date]
    			])->sum('sum');
    		}
			return json_encode(['sum'=>$sum]);
    	}
    }


    public function deleteReceiptArchive(Request $request)
	{
		$id = $request->input('action');
		ReceiptArchive::where('id', $id)->delete();

		return redirect()->to(session('this_previous_url'))->with('status', 'Строка успешно удалена!');
	}

}
