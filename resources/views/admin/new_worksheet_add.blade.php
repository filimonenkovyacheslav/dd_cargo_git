@extends('layouts.admin')
@section('content')

@can('editPost')
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
				<div class="card">
					<div class="card-header">
						<strong class="card-title">{{ $title }}</strong>
					</div>
					<div class="card-body">					

						{!! Form::open(['url'=>route('newWorksheetAdd'),'onsubmit' => 'сonfirmSigned(event)', 'class'=>'form-horizontal worksheet-add-form','method' => 'POST']) !!}

						<div class="form-group">
							{!! Form::label('site_name','Сайт',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::select('site_name', array('DD-C' => 'DD-C', 'For' => 'For'), '',['class' => 'form-control']) !!}
							</div>
						</div>
						
						{!! Form::hidden('date',date("Y.m.d"))!!}
						
						<div class="form-group">
							{!! Form::label('direction','Направление',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('direction',old('direction'),['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('tariff','Тариф',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::select('tariff', array('' => '', 'Море' => 'Море', 'Авиа' => 'Авиа'), '',['class' => 'form-control']) !!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('status','Статус',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::select('status', array('' => '', 'Доставляется на склад в стране отправителя' => 'Доставляется на склад в стране отправителя', 'На складе в стране отправителя' => 'На складе в стране отправителя', 'На таможне в стране отправителя' => 'На таможне в стране отправителя', 'Доставляется в страну получателя' => 'Доставляется в страну получателя', 'На таможне в стране получателя' => 'На таможне в стране получателя', 'Доставляется получателю' => 'Доставляется получателю', 'Доставлено' => 'Доставлено', 'Возврат' => 'Возврат', 'Коробка' => 'Коробка', 'Забрать' => 'Забрать', 'Уточнить' => 'Уточнить', 'Думают' => 'Думают', 'Отмена' => 'Отмена'), '',['class' => 'form-control']) !!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('partner','Партнер',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::select('partner', array('' => '', 'viewer_1' => 'viewer_1', 'viewer_2' => 'viewer_2', 'viewer_3' => 'viewer_3', 'viewer_4' => 'viewer_4', 'viewer_5' => 'viewer_5'), '',['class' => 'form-control']) !!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('tracking_main','Трекинг Основной',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('tracking_main',old('tracking_main'),['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('tracking_local','Трекинг Локальные',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('tracking_local',old('tracking_local'),['class' => 'form-control'])!!}
							</div>
						</div>

						@can('update-user')
						
						<div class="form-group">
							{!! Form::label('tracking_transit','Трекинг Транзитные',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('tracking_transit',old('tracking_transit'),['class' => 'form-control'])!!}
							</div>
						</div>
						
						@endcan

						<div class="form-group">
							{!! Form::label('pallet_number','Номер паллеты',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('pallet_number',old('pallet_number'),['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('comment_2','OFF Коммент',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('comment_2',old('comment_2'),['class' => 'form-control'])!!}
							</div>
						</div>

						@can('update-user')
						
						<div class="form-group">
							{!! Form::label('comments','DIR Комментарии',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('comments',old('comments'),['class' => 'form-control'])!!}
							</div>
						</div>

						@endcan
						
						<div class="form-group">
							{!! Form::label('sender_name','Отправитель',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('sender_name',old('sender_name'),['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('sender_country','Страна отправителя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('sender_country',old('sender_country'),['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('sender_city','Город отправителя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('sender_city',old('sender_city'),['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('sender_postcode','Индекс отправителя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('sender_postcode',old('sender_postcode'),['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('sender_address','Адрес отправителя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('sender_address',old('sender_address'),['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('standard_phone','Телефон отправителя (стандарт)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('standard_phone',old('standard_phone'),['class' => 'standard-phone form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('sender_phone','Телефон отправителя (дополнительно)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('sender_phone',old('sender_phone'),['class' => 'form-control'])!!}
							</div>
						</div>						
						<div class="form-group">
							{!! Form::label('sender_passport','Номер паспорта отправителя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('sender_passport',old('sender_passport'),['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_name','Получатель',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_name',old('recipient_name'),['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_country','Страна получателя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_country',old('recipient_country'),['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_city','Город получателя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_city',old('recipient_city'),['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_postcode','Индекс получателя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_postcode',old('recipient_postcode'),['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_street','Улица получателя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_street',old('recipient_street'),['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_house','Номер дома получателя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_house',old('recipient_house'),['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_room','Номер квартиры получателя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_room',old('recipient_room'),['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_phone','Телефон получателя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_phone',old('recipient_phone'),['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_passport','Номер паспорта получателя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_passport',old('recipient_passport'),['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_email','E-mail получателя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_email',old('recipient_email'),['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('package_content','Содержимое посылки',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('package_content',old('package_content'),['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('package_cost','Декларируемая стоимость посылки',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('package_cost',old('package_cost'),['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('courier','Курьер',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('courier',old('courier'),['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('pick_up_date','Дата забора и комментарии',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('pick_up_date',old('pick_up_date'),['class' => 'form-control'])!!}
							</div>
						</div>

						@can('update-user')
						
						<div class="form-group">
							{!! Form::label('weight','Вес посылки',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('weight',old('weight'),['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('width','Ширина',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('width',old('width'),['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('height','Высота',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('height',old('height'),['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('length','Длина',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('length',old('length'),['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('volume_weight','Объемный вес',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('volume_weight',old('volume_weight'),['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('quantity_things','Кол-во предметов',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('quantity_things',old('quantity_things'),['class' => 'form-control'])!!}
							</div>
						</div>
						
						@endcan

						<div class="form-group">
							{!! Form::label('batch_number','Партия',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('batch_number',old('batch_number'),['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('pay_date','Дата оплаты и комментарии',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('pay_date',old('pay_date'),['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('pay_sum','Сумма оплаты',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('pay_sum',old('pay_sum'),['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('status_en_disabled','Статус (ENG)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('status_en_disabled',old('status_en'),['class' => 'form-control', 'disabled' => 'disabled'])!!}
								{!! Form::hidden('status_en',old('status_en'),[])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('status_he_disabled','Статус (HE)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('status_he_disabled',old('status_he'),['class' => 'form-control', 'disabled' => 'disabled'])!!}
								{!! Form::hidden('status_he',old('status_he'),[])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('status_ua_disabled','Статус (UA)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('status_ua_disabled',old('status_ua'),['class' => 'form-control', 'disabled' => 'disabled'])!!}
								{!! Form::hidden('status_ua',old('status_ua'),[])!!}
							</div>
						</div>

						@if($new_column_1)
						<div class="form-group">
							{!! Form::label('new_column_1','Дополнительная колонка 1',['class' => 'col-md-2 control-label']) !!}
							<div class="col-md-8">
								{!! Form::text('new_column_1',old('new_column_1'),['class' => 'form-control']) !!}
							</div>
						</div>	
						@endif

						@if($new_column_2)
						<div class="form-group">
							{!! Form::label('new_column_2','Дополнительная колонка 2',['class' => 'col-md-2 control-label']) !!}
							<div class="col-md-8">
								{!! Form::text('new_column_2',old('new_column_2'),['class' => 'form-control']) !!}
							</div>
						</div>	
						@endif

						@if($new_column_3)
						<div class="form-group">
							{!! Form::label('new_column_3','Дополнительная колонка 3',['class' => 'col-md-2 control-label']) !!}
							<div class="col-md-8">
								{!! Form::text('new_column_3',old('new_column_3'),['class' => 'form-control']) !!}
							</div>
						</div>	
						@endif

						@if($new_column_4)
						<div class="form-group">
							{!! Form::label('new_column_4','Дополнительная колонка 4',['class' => 'col-md-2 control-label']) !!}
							<div class="col-md-8">
								{!! Form::text('new_column_4',old('new_column_4'),['class' => 'form-control']) !!}
							</div>
						</div>	
						@endif

						@if($new_column_5)
						<div class="form-group">
							{!! Form::label('new_column_5','Дополнительная колонка 5',['class' => 'col-md-2 control-label']) !!}
							<div class="col-md-8">
								{!! Form::text('new_column_5',old('new_column_5'),['class' => 'form-control']) !!}
							</div>
						</div>	
						@endif

						<div class="form-group">
							{!! Form::label('recipient_name_customs','Получатель (для таможни)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_name_customs',old('recipient_name_customs'),['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_country_customs','Страна получателя (для таможни)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_country_customs',old('recipient_country_customs'),['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_city_customs','Город получателя (для таможни)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_city_customs',old('recipient_city_customs'),['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_postcode_customs','Индекс получателя (для таможни)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_postcode_customs',old('recipient_postcode_customs'),['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_street_customs','Улица получателя (для таможни)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_street_customs',old('recipient_street_customs'),['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_house_customs','Номер дома получателя (для таможни)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_house_customs',old('recipient_house_customs'),['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_room_customs','Номер квартиры получателя (для таможни)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_room_customs',old('recipient_room_customs'),['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_phone_customs','Телефон получателя (для таможни)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_phone_customs',old('recipient_phone_customs'),['class' => 'form-control'])!!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('recipient_passport_customs','Номер паспорта получателя (для таможни)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('recipient_passport_customs',old('recipient_passport_customs'),['class' => 'form-control'])!!}
							</div>
						</div>
						
					
						{!! Form::button('Сохранить',['class'=>'btn btn-primary','type'=>'submit']) !!}
						{!! Form::close() !!}

					</div>
				</div>
			</div>


        </div>
    </div><!-- .animated -->
</div><!-- .content -->

<script type="text/javascript">
	function сonfirmSigned(event)
    {
        event.preventDefault();
        const form = event.target;
        
        const phone = document.querySelector('[name="standard_phone"]'); 
        if (phone.value.length < 10 || phone.value.length > 13) {
            alert('Кол-во знаков в телефоне должно быть от 10 до 13 !');
            return false;
        }

        form.submit();
    }
</script>

@else
<h1>Вы не можете просматривать эту страницу!</h1>
@endcan 
@endsection