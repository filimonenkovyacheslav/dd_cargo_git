@extends('layouts.admin')
@section('content')

@can('editPost')
<!-- <div class="breadcrumbs">
	<div class="col-sm-4">
		<div class="page-header float-left">
			<div class="page-title">
				<h1>Control Panel</h1>
			</div>
		</div>
	</div>
	<div class="col-sm-8">
		<div class="page-header float-right">
			<div class="page-title">
				<ol class="breadcrumb text-right">
					<li><a href="{{route('adminPhilIndIndex')}}">Control Panel</a></li>
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

						{!! Form::open(['url'=>route('addPackingSea'), 'class'=>'form-horizontal','method' => 'POST']) !!}

						<div class="form-group">
							{!! Form::label('payer','Плательщик',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('payer',old('payer'),['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('contract','Contract Nr.',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('contract',old('contract'),['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('type','Type',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('type',old('type'),['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('track_code','Trek-KOD',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('track_code',old('track_code'),['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('full_shipper','ФИО Отправителя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('full_shipper',old('full_shipper'),['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('full_consignee','ФИО получателя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('full_consignee',old('full_consignee'),['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('country_code','Код Страны',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('country_code',old('country_code'),['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('postcode','Индекс',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('postcode',old('postcode'),['class' => 'form-control'])!!}
							</div>
						</div>												
						
						<div class="form-group">
							{!! Form::label('region','Регион',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('region',old('region'),['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('district','Район',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('district',old('district'),['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('city','Город доставки',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('city',old('city'),['class' => 'form-control'])!!}
							</div>
						</div>						

						<div class="form-group">
							{!! Form::label('street','улица',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('street',old('street'),['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('house','дом',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('house',old('house'),['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('body','корпус',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('body',old('body'),['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('room','квартира',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('room',old('room'),['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('phone','Телефон(+7ххххх)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('phone',old('phone'),['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('tariff','Tarif €',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('tariff',old('tariff'),['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('tariff_cent','Tarif €-cent',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('tariff_cent',old('tariff_cent'),['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('weight_kg','weight kg',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('weight_kg',old('weight_kg'),['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('weight_g','weight g',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('weight_g',old('weight_g'),['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('service_code','код услуги',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('service_code',old('service_code'),['class' => 'form-control'])!!}
							</div>
						</div>	

						<div class="form-group">
							{!! Form::label('amount_1','Amount of COD Rbl',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('amount_1',old('amount_1'),['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('amount_2','Amount of COD kop',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('amount_2',old('amount_2'),['class' => 'form-control'])!!}
							</div>
						</div>												
						
						<div class="form-group">
							{!! Form::label('attachment_number','номер вложения',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('attachment_number',old('attachment_number'),['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('attachment_name','Наименования вложения',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('attachment_name',old('attachment_name'),['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('amount_3','Количество вложений',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('amount_3',old('amount_3'),['class' => 'form-control'])!!}
							</div>
						</div>						

						<div class="form-group">
							{!! Form::label('weight_enclosures_kg','weight of enclosures kg',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('weight_enclosures_kg',old('weight_enclosures_kg'),['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('weight_enclosures_g','weight of enclosures g',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('weight_enclosures_g',old('weight_enclosures_g'),['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('value_euro','стоимость евро',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('value_euro',old('value_euro'),['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('value_cent','стоимость евроценты',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('value_cent',old('value_cent'),['class' => 'form-control'])!!}
							</div>
						</div>																			
					
						{!! Form::button('Save',['class'=>'btn btn-primary','type'=>'submit']) !!}
						{!! Form::close() !!}

					</div>
				</div>
			</div>


        </div>
    </div><!-- .animated -->
</div><!-- .content -->

@else
<h1>You cannot view this page!</h1>
@endcan 
@endsection