@extends('layouts.admin')
@section('content')
<!-- <div class="breadcrumbs">
	<div class="col-sm-4">
		<div class="page-header float-left">
			<div class="page-title">
				<h1>Панель управления</h1>
			</div>
		</div>
	</div>
	<div class="col-sm-8">
		<div class="page-header float-right">
			<div class="page-title">
				<ol class="breadcrumb text-right">
					<li><a href="{{route('adminIndex')}}">Панель управления</a></li>
					<li class="active">{{ $title }}</li>
				</ol>                        
			</div>
		</div>
	</div>
</div> -->
<div class="content mt-3">
	<div class="animated fadeIn">
		<div class="row">
			<div class="col-md-12">
				<a href="{{ route('exportExcelCourierDraft') }}" style="margin-bottom: 20px;" class="btn btn-success btn-move">Экспорт в Excel</a>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-header">
						<strong class="card-title">{{ $title }}</strong>
					</div>

					@if (session('status-error'))
					<div class="alert alert-danger">
						{{ session('status-error') }}
					</div>
					@elseif (session('status'))
					<div class="alert alert-success">
						{{ session('status') }}
					</div>
					@endif

					@php
						session(['this_previous_url' => url()->full()]);
					@endphp

					<div class="btn-move-wrapper" style="display:flex">
						<form action="{{ route('courierDraftWorksheetFilter') }}" method="GET" id="form-worksheet-table-filter" enctype="multipart/form-data">
							@csrf
							<label class="table_columns" style="margin: 0 15px">Выберите колонку:
								<select class="form-control" id="table_columns" name="table_columns">
									<option value="" selected="selected"></option>
									<option value="site_name">Сайт</option>
									<option value="date">Дата</option>
									<option value="direction">Направление</option>
									<option value="tariff">Тариф</option>
									<option value="status">Статус</option>
									<option value="status_date">Дата Статуса</option>
									<option value="partner">Партнер</option>
									<option value="tracking_main">Основной</option>
									<option value="tracking_local">Локальный</option>
									<option value="tracking_transit">Транзитный</option>
									<option value="comment_2">Коммент</option>
									<option value="comments">Комментарии</option>
									<option value="sender_name">Отправитель</option>
									<option value="sender_country">Страна отправителя</option>
									<option value="shipper_region">Регион отправителя</option>
									<option value="sender_city">Город отправителя</option>
									<option value="sender_postcode">Индекс отправителя</option>
									<option value="sender_address">Адрес отправителя</option>
									<option value="standard_phone">Телефон (стандарт)</option>
									<option value="sender_phone">Телефон (дополнительно)</option>
									<option value="sender_passport">Номер паспорта отправителя</option>
									<option value="recipient_name">Получатель</option>
									<option value="recipient_country">Страна получателя</option>
									<option value="region">Регион</option>
									<option value="district">Район</option>
									<option value="recipient_city">Город получателя</option>
									<option value="recipient_postcode">Индекс получателя</option>
									<option value="recipient_street">Улица получателя</option>
									<option value="recipient_house">№ дома пол-ля</option>
									<option value="body">корпус</option>
									<option value="recipient_room">№ кв. пол-ля</option>
									<option value="recipient_phone">Телефон получателя</option>
									<option value="recipient_passport">Номер паспорта получателя</option>
									<option value="recipient_email">E-mail получателя</option>
									<option value="package_cost">Стоимость посылки</option>
									<option value="courier">Курьер</option>
									<option value="pick_up_date">Дата забора и комментарии</option>
									<option value="weight">Вес посылки</option>
									<option value="width">Ширина</option>
									<option value="height">Высота</option>
									<option value="length">Длина</option>
									<option value="volume_weight">Объемный вес</option>
									<option value="quantity_things">Кол-во предметов</option>
									<option value="pay_date">Дата оплаты и комментарии</option>
									<option value="pay_sum">Сумма оплаты</option>
									<option value="status_en">ENG Статус</option>
									<option value="status_he">HE Статус</option>
									<option value="status_ua">UA Статус</option>                  
								</select>
							</label>
							<label>Фильтр:
								<input type="search" name="table_filter_value" class="form-control form-control-sm">
							</label>

							<input type="hidden" name="for_active">
							
							<button type="button" id="table_filter_button" style="margin-left:35px" class="btn btn-default">Искать</button>
						</form>

						<label style="margin-top: 30px;margin-left: 30px;">Показывать только записи для активации
							<input type="checkbox" onclick="forActivation(event)" class="for_active" style="width:20px;height:20px;">
						</label>
					
					</div>
					
					<div class="card-body new-worksheet">
						<div class="table-container">
							<table class="table table-striped table-bordered">
								<thead>
									<tr>
										<th>V</th>
										<th>Изменить</th>
										<th>Сайт</th>
										<th>Дата</th>
										<th>Off<hr>Направ- ление</th>
										<th>Тариф</th>
										<th>Статус</th>
										<th>Дата Статуса</th>
										<th>Партнер</th>
										<th>Трекинг<hr>Основной</th> 
										<th>№ заказа</th>
										<th>Кол-во посылок</th>
										<th>Трекинг<hr>Локальный</th>
										<th>Трекинг<hr>Транзитный</th>
										<th>Номер паллеты</th>
										<th>OFF<hr>Коммент</th>
										<th>DIR<hr>Комментарии</th>
										<th>Отправитель</th>
										<th>Страна отправителя</th>
										<th>Регион отправителя</th>
										<th>Город отправителя</th>
										<th>Индекс отправителя</th>
										<th>Адрес отправителя</th>
										<th>Телефон отправителя (стандарт)</th>
										<th>Телефон отправителя (дополнительно)</th>
										<th>Номер паспорта отправителя</th>
										<th>Получатель</th>
										<th>Страна получателя</th>
										<th>Регион</th>
										<th>Район</th>
										<th>Город получателя</th>
										<th>Индекс получателя</th>
										<th>Улица получателя</th>
										<th>№ дома пол-ля</th>
										<th>корпус</th>
										<th>№ кв. пол- ля</th>
										<th>Телефон получателя</th>
										<th>Номер паспорта получателя</th>
										<th>E-mail получателя</th>										
										<th>Стоимость посылки</th>
										<th>Курьер</th>
										<th>Дата забора и комментарии</th>
										<th>Вес посылки</th>
										<th>Ширина</th>
										<th>Высота</th>
										<th>Длина</th>
										<th>Объемный вес</th>
										<th>Кол-во предметов</th>
										<th>Партия</th>									
										<th>Дата оплаты и комментарии</th>
										<th>Сумма оплаты</th>
										<th>ENG<hr>Статус</th>
										<th>HE<hr>Статус</th>
										<th>UA<hr>Статус</th>					
										<th>Содержимое посылки</th>
										<th>Получатель (для таможни)</th>
										<th>Страна получателя (для таможни)</th>
										<th>Город получателя (для таможни)</th>
										<th>Индекс получателя (для таможни)</th>
										<th>Улица получателя (для таможни)</th>
										<th>№ дома пол-ля (для таможни)</th>
										<th>№ кв. пол- ля (для таможни)</th>
										<th>Телефон получателя (для таможни)</th>
										<th>Номер паспорта получателя (для таможни)</th>
									</tr>

								</thead>
								<tbody>

									@php
									$id_arr = [];
									@endphp

									@if(isset($courier_draft_worksheet_arr))
									@for($i=0; $i < count($courier_draft_worksheet_arr); $i++)
									@foreach($courier_draft_worksheet_arr[$i] as $row)

									@if (!in_array($row->id, $id_arr))
									@php
									$id_arr[] = $row->id;
									@endphp

									@if(!in_array($user->role, $viewer_arr))

									<tr>
										<td class="td-checkbox">
											<input type="checkbox" name="row_id[]" value="{{ $row->id }}">
										</td>
										<td class="td-button">
											
											@can('editDraft')
											
											<a class="btn btn-primary" href="{{ url('/admin/courier-draft-worksheet/'.$row->id) }}">Изменить</a>

											<a class="btn btn-default" onclick="ConfirmDouble(event)" href="{{ url('/admin/courier-draft-worksheet-double/'.$row->id) }}">Дубль</a>

											@endcan

											@can('activateDraft')

											<a class="btn btn-success" data-id="{{ $row->id }}" onclick="ConfirmActivate(event)" href="{{ url('/admin/courier-draft-check-activate/'.$row->id) }}">Активировать</a>

											@endcan

											@can('editPost')

											{!! Form::open(['url'=>route('deleteCourierDraftWorksheet'), 'class'=>'form-horizontal','method' => 'POST']) !!}
											{!! Form::hidden('action',$row->id) !!}
											{!! Form::button('Удалить',['class'=>'btn btn-danger','type'=>'submit','onclick' => 'ConfirmDelete(event)']) !!}
											{!! Form::close() !!}

											@endcan
										</td> 										
										<td title="{{$row->site_name}}">
											<div class="div-22">{{$row->site_name}}</div>
										</td>
										<td title="{{$row->date}}">
											<div class="div-1">{{$row->date}}</div>
										</td>
										<td title="{{$row->direction}}">
											<div class="div-2">{{$row->direction}}</div>
										</td>
										<td title="{{$row->tariff}}">
											<div class="div-2">{{$row->tariff}}</div>
										</td>
										<td title="{{$row->status}}">
											<div class="div-3">{{$row->status}}</div>
										</td>
										<td title="{{$row->status_date}}">
											<div class="div-1">{{$row->status_date}}</div>
										</td>
										<td title="{{$row->partner}}">
											<div class="div-3">{{$row->partner}}</div>
										</td>										
										<td title="{{$row->tracking_main}}">
											<div class="div-4">{{$row->tracking_main}}</div>
										</td>
										<td class="td-button" title="{{$row->order_number}}">
											<div class="div-22">{{$row->order_number}}</div>
										</td>
										<td title="{{$row->parcels_qty}}">
											<div class="div-22">{{$row->parcels_qty}}</div>
										</td>
										<td title="{{$row->tracking_local}}">
											<div class="div-5">{{$row->tracking_local}}</div>
										</td>
										<td title="{{$row->tracking_transit}}">
											<div class="div-6">{{$row->tracking_transit}}</div>
										</td>
										<td title="{{$row->pallet_number}}">
											<div class="div-7">{{$row->pallet_number}}</div>
										</td>
										<td title="{{$row->comment_2}}">
											<div class="div-8">{{$row->comment_2}}</div>
										</td>
										<td title="{{$row->comments}}">
											<div class="div-9">{{$row->comments}}</div>
										</td>
										<td title="{{$row->sender_name}}">
											<div class="div-10">{{$row->sender_name}}</div>
										</td>
										<td title="{{$row->sender_country}}">
											<div class="div-11">{{$row->sender_country}}</div>
										</td>
										<td title="{{$row->shipper_region}}">
											<div class="div-2">{{$row->shipper_region}}</div>
										</td>
										<td title="{{$row->sender_city}}">
											<div class="div-12">{{$row->sender_city}}</div>
										</td>
										<td title="{{$row->sender_postcode}}">
											<div class="div-13">{{$row->sender_postcode}}</div>
										</td>
										<td title="{{$row->sender_address}}">
											<div class="div-14">{{$row->sender_address}}</div>
										</td>
										<td title="{{$row->standard_phone}}">
											<div class="div-15">{{$row->standard_phone}}</div>
										</td>
										<td title="{{$row->sender_phone}}">
											<div class="div-15">{{$row->sender_phone}}</div>
										</td>
										<td title="{{$row->sender_passport}}">
											<div class="div-16">{{$row->sender_passport}}</div>
										</td>
										<td title="{{$row->recipient_name}}">
											<div class="div-17">{{$row->recipient_name}}</div>
										</td>
										<td title="{{$row->recipient_country}}">
											<div class="div-18">{{$row->recipient_country}}</div>
										</td>
										<td title="{{$row->region}}">
											<div class="div-18">{{$row->region}}</div>
										</td>
										<td title="{{$row->district}}">
											<div class="div-18">{{$row->district}}</div>
										</td>
										<td title="{{$row->recipient_city}}">
											<div class="div-19">{{$row->recipient_city}}</div>
										</td>
										<td title="{{$row->recipient_postcode}}">
											<div class="div-20">{{$row->recipient_postcode}}</div>
										</td>
										<td title="{{$row->recipient_street}}">
											<div class="div-21">{{$row->recipient_street}}</div>
										</td>
										<td title="{{$row->recipient_house}}">
											<div class="div-22">{{$row->recipient_house}}</div>
										</td>
										<td title="{{$row->body}}">
											<div class="div-22">{{$row->body}}</div>
										</td>
										<td title="{{$row->recipient_room}}">
											<div class="div-23">{{$row->recipient_room}}</div>
										</td>
										<td title="{{$row->recipient_phone}}">
											<div class="div-24">{{$row->recipient_phone}}</div>
										</td>
										<td title="{{$row->recipient_passport}}">
											<div class="div-25">{{$row->recipient_passport}}</div>
										</td>
										<td title="{{$row->recipient_email}}">
											<div class="div-26">{{$row->recipient_email}}</div>
										</td>
										<td title="{{$row->package_cost}}">
											<div class="div-27">{{$row->package_cost}}</div>
										</td>
										<td title="{{$row->courier}}">
											<div class="div-28">{{$row->courier}}</div>
										</td>
										<td title="{{$row->pick_up_date}}">
											<div class="div-29">{{$row->pick_up_date}}</div>
										</td>
										<td title="{{$row->weight}}">
											<div class="div-30">{{$row->weight}}</div>
										</td>
										<td title="{{$row->width}}">
											<div class="div-31">{{$row->width}}</div>
										</td>
										<td title="{{$row->height}}">
											<div class="div-32">{{$row->height}}</div>
										</td>
										<td title="{{$row->length}}">
											<div class="div-33">{{$row->length}}</div>
										</td>
										<td title="{{$row->volume_weight}}">
											<div class="div-34">{{$row->volume_weight}}</div>
										</td>
										<td title="{{$row->quantity_things}}">
											<div class="div-35">{{$row->quantity_things}}</div>
										</td>
										<td title="{{$row->batch_number}}">
											<div class="div-36">{{$row->batch_number}}</div>
										</td>
										<td title="{{$row->pay_date}}">
											<div class="div-37">{{$row->pay_date}}</div>
										</td>
										<td title="{{$row->pay_sum}}">
											<div class="div-38">{{$row->pay_sum}}</div>
										</td>
										<td title="{{$row->status_en}}">
											<div class="div-39">{{$row->status_en}}</div>
										</td>
										<td title="{{$row->status_he}}">
											<div class="div-40">{{$row->status_he}}</div>
										</td> 
										<td title="{{$row->status_ua}}">
											<div class="div-41">{{$row->status_ua}}</div>
										</td>										
										<td title="{{$row->package_content}}">
											<div class="div1">{{$row->package_content}}</div>
										</td>
										<td title="{{$row->recipient_name_customs}}">
											<div class="div-17">{{$row->recipient_name_customs}}</div>
										</td>
										<td title="{{$row->recipient_country_customs}}">
											<div class="div-18">{{$row->recipient_country_customs}}</div>
										</td>
										<td title="{{$row->recipient_city_customs}}">
											<div class="div-19">{{$row->recipient_city_customs}}</div>
										</td>
										<td title="{{$row->recipient_postcode_customs}}">
											<div class="div-20">{{$row->recipient_postcode_customs}}</div>
										</td>
										<td title="{{$row->recipient_street_customs}}">
											<div class="div-21">{{$row->recipient_street_customs}}</div>
										</td>
										<td title="{{$row->recipient_house_customs}}">
											<div class="div-22">{{$row->recipient_house_customs}}</div>
										</td>
										<td title="{{$row->recipient_room_customs}}">
											<div class="div-23">{{$row->recipient_room_customs}}</div>
										</td>
										<td title="{{$row->recipient_phone_customs}}">
											<div class="div-24">{{$row->recipient_phone_customs}}</div>
										</td>
										<td title="{{$row->recipient_passport_customs}}">
											<div class="div-25">{{$row->recipient_passport_customs}}</div>
										</td>                                                                  
									</tr>

									@elseif($row->partner === $user->role)

									<tr>
										<td class="td-checkbox">
											<input type="checkbox" name="row_id[]" value="{{ $row->id }}">
										</td>
										<td class="td-button">
											
											@can('editDraft')
											
											<a class="btn btn-primary" href="{{ url('/admin/courier-draft-worksheet/'.$row->id) }}">Изменить</a>

											<a class="btn btn-default" onclick="ConfirmDouble(event)" href="{{ url('/admin/courier-draft-worksheet-double/'.$row->id) }}">Дубль</a>

											@endcan

											@can('activateDraft')

											<a class="btn btn-success" data-id="{{ $row->id }}" onclick="ConfirmActivate(event)" href="{{ url('/admin/courier-draft-check-activate/'.$row->id) }}">Активировать</a>

											@endcan

											@can('editPost')

											{!! Form::open(['url'=>route('deleteCourierDraftWorksheet'), 'class'=>'form-horizontal','method' => 'POST']) !!}
											{!! Form::hidden('action',$row->id) !!}
											{!! Form::button('Удалить',['class'=>'btn btn-danger','type'=>'submit','onclick' => 'ConfirmDelete(event)']) !!}
											{!! Form::close() !!}

											@endcan
										</td>
										<td title="{{$row->site_name}}">
											<div class="div-22">{{$row->site_name}}</div>
										</td>
										<td title="{{$row->date}}">
											<div class="div-1">{{$row->date}}</div>
										</td>
										<td title="{{$row->direction}}">
											<div class="div-2">{{$row->direction}}</div>
										</td>
										<td title="{{$row->tariff}}">
											<div class="div-2">{{$row->tariff}}</div>
										</td>
										<td title="{{$row->status}}">
											<div class="div-3">{{$row->status}}</div>
										</td>
										<td title="{{$row->status_date}}">
											<div class="div-1">{{$row->status_date}}</div>
										</td>
										<td title="{{$row->partner}}">
											<div class="div-3">{{$row->partner}}</div>
										</td>
										<td title="{{$row->tracking_main}}">
											<div class="div-4">{{$row->tracking_main}}</div>
										</td>
										<td class="td-button" title="{{$row->order_number}}">
											<div class="div-22">{{$row->order_number}}</div>
										</td>
										<td title="{{$row->parcels_qty}}">
											<div class="div-22">{{$row->parcels_qty}}</div>
										</td>
										<td title="{{$row->tracking_local}}">
											<div class="div-5">{{$row->tracking_local}}</div>
										</td>
										<td title="{{$row->tracking_transit}}">
											<div class="div-6">{{$row->tracking_transit}}</div>
										</td>
										<td title="{{$row->pallet_number}}">
											<div class="div-7">{{$row->pallet_number}}</div>
										</td>
										<td title="{{$row->comment_2}}">
											<div class="div-8">{{$row->comment_2}}</div>
										</td>
										<td title="{{$row->comments}}">
											<div class="div-9">{{$row->comments}}</div>
										</td>
										<td title="{{$row->sender_name}}">
											<div class="div-10">{{$row->sender_name}}</div>
										</td>
										<td title="{{$row->sender_country}}">
											<div class="div-11">{{$row->sender_country}}</div>
										</td>
										<td title="{{$row->shipper_region}}">
											<div class="div-2">{{$row->shipper_region}}</div>
										</td>
										<td title="{{$row->sender_city}}">
											<div class="div-12">{{$row->sender_city}}</div>
										</td>
										<td title="{{$row->sender_postcode}}">
											<div class="div-13">{{$row->sender_postcode}}</div>
										</td>
										<td title="{{$row->sender_address}}">
											<div class="div-14">{{$row->sender_address}}</div>
										</td>
										<td title="{{$row->standard_phone}}">
											<div class="div-15">{{$row->standard_phone}}</div>
										</td>
										<td title="{{$row->sender_phone}}">
											<div class="div-15">{{$row->sender_phone}}</div>
										</td>
										<td title="{{$row->sender_passport}}">
											<div class="div-16">{{$row->sender_passport}}</div>
										</td>
										<td title="{{$row->recipient_name}}">
											<div class="div-17">{{$row->recipient_name}}</div>
										</td>
										<td title="{{$row->recipient_country}}">
											<div class="div-18">{{$row->recipient_country}}</div>
										</td>
										<td title="{{$row->region}}">
											<div class="div-18">{{$row->region}}</div>
										</td>
										<td title="{{$row->district}}">
											<div class="div-18">{{$row->district}}</div>
										</td>
										<td title="{{$row->recipient_city}}">
											<div class="div-19">{{$row->recipient_city}}</div>
										</td>
										<td title="{{$row->recipient_postcode}}">
											<div class="div-20">{{$row->recipient_postcode}}</div>
										</td>
										<td title="{{$row->recipient_street}}">
											<div class="div-21">{{$row->recipient_street}}</div>
										</td>
										<td title="{{$row->recipient_house}}">
											<div class="div-22">{{$row->recipient_house}}</div>
										</td>
										<td title="{{$row->body}}">
											<div class="div-22">{{$row->body}}</div>
										</td>
										<td title="{{$row->recipient_room}}">
											<div class="div-23">{{$row->recipient_room}}</div>
										</td>
										<td title="{{$row->recipient_phone}}">
											<div class="div-24">{{$row->recipient_phone}}</div>
										</td>
										<td title="{{$row->recipient_passport}}">
											<div class="div-25">{{$row->recipient_passport}}</div>
										</td>
										<td title="{{$row->recipient_email}}">
											<div class="div-26">{{$row->recipient_email}}</div>
										</td>
										<td title="{{$row->package_cost}}">
											<div class="div-27">{{$row->package_cost}}</div>
										</td>
										<td title="{{$row->courier}}">
											<div class="div-28">{{$row->courier}}</div>
										</td>
										<td title="{{$row->pick_up_date}}">
											<div class="div-29">{{$row->pick_up_date}}</div>
										</td>
										<td title="{{$row->weight}}">
											<div class="div-30">{{$row->weight}}</div>
										</td>
										<td title="{{$row->width}}">
											<div class="div-31">{{$row->width}}</div>
										</td>
										<td title="{{$row->height}}">
											<div class="div-32">{{$row->height}}</div>
										</td>
										<td title="{{$row->length}}">
											<div class="div-33">{{$row->length}}</div>
										</td>
										<td title="{{$row->volume_weight}}">
											<div class="div-34">{{$row->volume_weight}}</div>
										</td>
										<td title="{{$row->quantity_things}}">
											<div class="div-35">{{$row->quantity_things}}</div>
										</td>
										<td title="{{$row->batch_number}}">
											<div class="div-36">{{$row->batch_number}}</div>
										</td>
										<td title="{{$row->pay_date}}">
											<div class="div-37">{{$row->pay_date}}</div>
										</td>
										<td title="{{$row->pay_sum}}">
											<div class="div-38">{{$row->pay_sum}}</div>
										</td>
										<td title="{{$row->status_en}}">
											<div class="div-39">{{$row->status_en}}</div>
										</td>
										<td title="{{$row->status_he}}">
											<div class="div-40">{{$row->status_he}}</div>
										</td> 
										<td title="{{$row->status_ua}}">
											<div class="div-41">{{$row->status_ua}}</div>
										</td>
										<td title="{{$row->package_content}}">
											<div class="div1">{{$row->package_content}}</div>
										</td>
										<td title="{{$row->recipient_name_customs}}">
											<div class="div-17">{{$row->recipient_name_customs}}</div>
										</td>
										<td title="{{$row->recipient_country_customs}}">
											<div class="div-18">{{$row->recipient_country_customs}}</div>
										</td>
										<td title="{{$row->recipient_city_customs}}">
											<div class="div-19">{{$row->recipient_city_customs}}</div>
										</td>
										<td title="{{$row->recipient_postcode_customs}}">
											<div class="div-20">{{$row->recipient_postcode_customs}}</div>
										</td>
										<td title="{{$row->recipient_street_customs}}">
											<div class="div-21">{{$row->recipient_street_customs}}</div>
										</td>
										<td title="{{$row->recipient_house_customs}}">
											<div class="div-22">{{$row->recipient_house_customs}}</div>
										</td>
										<td title="{{$row->recipient_room_customs}}">
											<div class="div-23">{{$row->recipient_room_customs}}</div>
										</td>
										<td title="{{$row->recipient_phone_customs}}">
											<div class="div-24">{{$row->recipient_phone_customs}}</div>
										</td>
										<td title="{{$row->recipient_passport_customs}}">
											<div class="div-25">{{$row->recipient_passport_customs}}</div>
										</td>                                                                     
									</tr>

									@endif
									@endif
									@endforeach
									@endfor
									@endif
								</tbody>
							</table>
							
							<div class="checkbox-operations">

								@can('editDraft')
								
								{!! Form::open(['url'=>route('addCourierDraftDataById'), 'class'=>'worksheet-add-form','method' => 'POST']) !!}
									
									<label>Выберите действие с выбранными строчками:
										<select class="form-control" name="checkbox_operations_select">
											<option value=""></option>

											@can('editPost')				
											
											<option value="delete">Удалить</option>

											@endcan
											
											<option value="change">Изменить</option>
										</select>
									</label>
									
									<label class="checkbox-operations-change">Выберите колонку:
										<select class="form-control" id="tracking-columns" name="tracking-columns">
											<option value="" selected="selected"></option>
											<option value="site_name">Сайт</option>
											<option value="direction">Направление</option>
											<option value="tariff">Тариф</option>
											<option value="status">Статус</option>
											<option value="partner">Партнер</option>
											<option value="parcels_qty">Кол-во посылок</option>
											<option value="tracking_local">Локальный</option>
											<option value="tracking_transit">Транзитный</option>
											<option value="pallet_number">№ паллеты</option>
											<option value="comment_2">Коммент</option>
											<option value="comments">Комментарии</option>
											<option value="sender_name">Отправитель</option>
											<option value="sender_country">Страна отправителя</option>
											<option value="sender_city">Город отправителя</option>
											<option value="sender_postcode">Индекс отправителя</option>
											<option value="sender_address">Адрес отправителя</option>
											<option value="sender_phone">Телефон отправителя</option>
											<option value="sender_passport">Номер паспорта отправителя</option>
											<option value="recipient_name">Получатель</option>
											<option value="recipient_country">Страна получателя</option>
											<option value="region">Регион</option>
											<option value="district">Район</option>
											<option value="recipient_city">Город получателя</option>
											<option value="recipient_postcode">Индекс получателя</option>
											<option value="recipient_street">Улица получателя</option>
											<option value="recipient_house">№ дома пол-ля</option>
											<option value="body">корпус</option>
											<option value="recipient_room">№ кв. пол-ля</option>
											<option value="recipient_phone">Телефон получателя</option>
											<option value="recipient_passport">Номер паспорта получателя</option>
											<option value="recipient_email">E-mail получателя</option>
											<option value="package_cost">Стоимость посылки</option>
											<option value="courier">Курьер</option>
											<option value="pick_up_date">Дата забора и комментарии</option>
											<option value="weight">Вес посылки</option>
											<option value="width">Ширина</option>
											<option value="height">Высота</option>
											<option value="length">Длина</option>
											<option value="volume_weight">Объемный вес</option>
											<option value="quantity_things">Кол-во предметов</option>
											<option value="batch_number">Партия</option>
											<option value="pay_date">Дата оплаты и комментарии</option>
											<option value="pay_sum">Сумма оплаты</option>  
										</select>
									</label>	

									<label class="value-by-tracking checkbox-operations-change">Введите значение:
										<input class="form-control" type="text" name="value-by-tracking">
										<input type="hidden" name="status_en">
										<input type="hidden" name="status_ua">
										<input type="hidden" name="status_he">
									</label>									

								{!! Form::button('Сохранить',['class'=>'btn btn-primary checkbox-operations-change','type'=>'submit']) !!}
								{!! Form::close() !!}

								{!! Form::open(['url'=>route('deleteCourierDraftWorksheetById'),'method' => 'POST']) !!}
								{!! Form::button('Удалить',['class'=>'btn btn-danger  checkbox-operations-delete','type'=>'submit','onclick' => 'ConfirmDelete(event)']) !!}
								{!! Form::close() !!}

								@endcan

							</div>

						</div>
					</div>
				</div>
			</div><!-- .col-md-12 -->
		</div><!-- .row -->		
		
	</div><!-- .animated -->
</div><!-- .content -->

<script>

	let href = location.href;
	if (href.indexOf('for_active') !== -1) {
		document.querySelector('.for_active').checked = true;
		document.querySelector('[name="for_active"]').value = 'for_active';
	}

	function ConfirmDelete(event)
	{
		event.preventDefault();
		const form = event.target.parentElement;
		const data = new URLSearchParams(new FormData(form)).toString();
		var x = confirm("Вы уверены, что хотите удалить окончательно?");
		if (x)
			form.submit();
		else
			location.href = '/admin/to-trash?'+data+'&table=courier_draft_worksheet';
	}

	function ConfirmDouble(event)
	{
		event.preventDefault();
		const href = $(event.target).attr('href');
		var x = confirm("Вы уверены, что хотите продублировать?");
		if (x)
			location.href = href;
		else
			return false;
	}

	function ConfirmActivate(event)
	{
		$('.alert.alert-danger').remove();
		var x = confirm("Вы уверены, что хотите активировать?");
		event.preventDefault();
		const href = $(event.target).attr('href');
		const rowId = $(event.target).attr('data-id');
		if (x){
			$.ajax({
				url: href,
				type: "GET",
				headers: {
					'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
				},
				success: function (data) {
					console.log(data);
					if (data.error) {
						$('.card-header').after(`
							<div class="alert alert-danger">
								`+data.error+`										
							</div>`)
						return 0;
					}
					else{
						location.href = '/admin/courier-draft-activate/'+rowId;
					}
				},
				error: function (msg) {
					alert('Ошибка admin');
				}
			});
		}	
	}

</script>

@endsection