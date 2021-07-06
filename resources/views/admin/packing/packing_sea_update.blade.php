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

					@if(isset($packing_sea))				

						{!! Form::open(['url'=>route('updatePackingSea', ['id'=>$packing_sea->id]), 'class'=>'form-horizontal','method' => 'POST']) !!}

						<div class="form-group">
							{!! Form::label('payer','Плательщик',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('payer',$packing_sea->payer,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('contract','Contract Nr.',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('contract',$packing_sea->contract,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('type','Type',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('type',$packing_sea->type,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('track_code','Trek-KOD',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('track_code',$packing_sea->track_code,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('full_shipper','ФИО Отправителя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('full_shipper',$packing_sea->full_shipper,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('full_consignee','ФИО получателя',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('full_consignee',$packing_sea->full_consignee,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('country_code','Код Страны',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('country_code',$packing_sea->country_code,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('postcode','Индекс',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('postcode',$packing_sea->postcode,['class' => 'form-control'])!!}
							</div>
						</div>												
						
						<div class="form-group">
							{!! Form::label('region','Регион',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('region',$packing_sea->region,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('district','Район',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('district',$packing_sea->district,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('city','Город доставки',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('city',$packing_sea->city,['class' => 'form-control'])!!}
							</div>
						</div>						

						<div class="form-group">
							{!! Form::label('street','улица',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('street',$packing_sea->street,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('house','дом',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('house',$packing_sea->house,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('body','корпус',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('body',$packing_sea->body,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('room','квартира',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('room',$packing_sea->room,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('phone','Телефон(+7ххххх)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('phone',$packing_sea->phone,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('tariff','Tarif €',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('tariff',$packing_sea->tariff,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('tariff_cent','Tarif €-cent',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('tariff_cent',$packing_sea->tariff_cent,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('weight_kg','weight kg',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('weight_kg',$packing_sea->weight_kg,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('weight_g','weight g',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('weight_g',$packing_sea->weight_g,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('service_code','код услуги',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('service_code',$packing_sea->service_code,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('amount_1','Amount of COD Rbl',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('amount_1',$packing_sea->amount_1,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('amount_2','Amount of COD kop',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('amount_2',$packing_sea->amount_2,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('attachment_number','номер вложения',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('attachment_number',$packing_sea->attachment_number,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('attachment_name','Наименования вложения',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('attachment_name',$packing_sea->attachment_name,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('amount_3','Количество вложений',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('amount_3',$packing_sea->amount_3,['class' => 'form-control'])!!}
							</div>
						</div>												
						
						<div class="form-group">
							{!! Form::label('weight_enclosures_kg','weight of enclosures kg',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('weight_enclosures_kg',$packing_sea->weight_enclosures_kg,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('weight_enclosures_g','weight of enclosures g',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('weight_enclosures_g',$packing_sea->weight_enclosures_g,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('value_euro','стоимость евро',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('value_euro',$packing_sea->value_euro,['class' => 'form-control'])!!}
							</div>
						</div>						

						<div class="form-group">
							{!! Form::label('value_cent','стоимость евроценты',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('value_cent',$packing_sea->value_cent,['class' => 'form-control'])!!}
							</div>
						</div>	

						{!! Form::hidden('work_sheet_id',$packing_sea->work_sheet_id) !!}					
										
						{!! Form::button('Save',['class'=>'btn btn-primary','type'=>'submit']) !!}
						{!! Form::close() !!}

					@endif
					
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