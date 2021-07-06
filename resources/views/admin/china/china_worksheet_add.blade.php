@extends('layouts.china_admin')
@section('content')

@can('china-update-post')
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
					<li><a href="{{route('adminChinaIndex')}}">Control Panel</a></li>
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

						{!! Form::open(['url'=>route('chinaWorksheetAdd'), 'class'=>'form-horizontal china-worksheet-form','method' => 'POST']) !!}

						<div class="form-group">
							{!! Form::label('date','Date',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('date',(old('date'))?old('date'):date("Y.m.d"),['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('tracking_main','Tracking number main',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('tracking_main',old('tracking_main'),['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('tracking_local','Local tracking number',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('tracking_local',old('tracking_local'),['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('status','Status',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::select('status', array('' => '', 'Pending' => 'Pending', 'Forwarding to the warehouse in the sender country' => 'Forwarding to the warehouse in the sender country', 'At the warehouse in the sender country' => 'At the warehouse in the sender country', 'At the customs in the sender country' => 'At the customs in the sender country', 'Forwarding to the receiver country' => 'Forwarding to the receiver country', 'At the customs in the receiver country' => 'At the customs in the receiver country', 'Forwarding to the receiver' => 'Forwarding to the receiver', 'Delivered' => 'Delivered', 'Return' => 'Return', 'Box' => 'Box', 'Pick up' => 'Pick up', 'Specify' => 'Specify', 'Think' => 'Think', 'Canceled' => 'Canceled'), '',['class' => 'form-control']) !!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('customer_name','Customer name',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('customer_name',old('customer_name'),['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('customer_address','Customer address',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('customer_address',old('customer_address'),['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('customer_phone','Customer phone number',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('customer_phone',old('customer_phone'),['class' => 'form-control'])!!}
							</div>
						</div>						

						<div class="form-group">
							{!! Form::label('customer_email','Customer email',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('customer_email',old('customer_email'),['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('supplier_name','Supplier name',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('supplier_name',old('supplier_name'),['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('supplier_address','Supplier address',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('supplier_address',old('supplier_address'),['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('supplier_phone','Supplier phone number',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('supplier_phone',old('supplier_phone'),['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('supplier_email','Supplier email',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('supplier_email',old('supplier_email'),['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('shipment_description','Shipment description',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('shipment_description',old('shipment_description'),['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('weight','Shipment weight',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('weight',old('weight'),['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('length','Shipment length',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('length',old('length'),['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('width','Shipment width',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('width',old('width'),['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('height','Shipment height',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('height',old('height'),['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('lot_number','Lot number',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('lot_number',old('lot_number'),['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('status_he_disabled','Status He',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('status_he_disabled',old('status_he'),['class' => 'form-control', 'disabled' => 'disabled'])!!}
								{!! Form::hidden('status_he',old('status_he'))!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('status_ru_disabled','Status Ru',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('status_ru_disabled',old('status_ru'),['class' => 'form-control', 'disabled' => 'disabled'])!!}
								{!! Form::hidden('status_ru',old('status_ru'))!!}
							</div>
						</div>

						
						@if($new_column_1)
						<div class="form-group">
							{!! Form::label('new_column_1','Additional column 1',['class' => 'col-md-2 control-label']) !!}
							<div class="col-md-8">
								{!! Form::text('new_column_1',old('new_column_1'),['class' => 'form-control']) !!}
							</div>
						</div>	
						@endif

						@if($new_column_2)
						<div class="form-group">
							{!! Form::label('new_column_2','Additional column 2',['class' => 'col-md-2 control-label']) !!}
							<div class="col-md-8">
								{!! Form::text('new_column_2',old('new_column_2'),['class' => 'form-control']) !!}
							</div>
						</div>	
						@endif

						@if($new_column_3)
						<div class="form-group">
							{!! Form::label('new_column_3','Additional column 3',['class' => 'col-md-2 control-label']) !!}
							<div class="col-md-8">
								{!! Form::text('new_column_3',old('new_column_3'),['class' => 'form-control']) !!}
							</div>
						</div>	
						@endif

						@if($new_column_4)
						<div class="form-group">
							{!! Form::label('new_column_4','Additional column 4',['class' => 'col-md-2 control-label']) !!}
							<div class="col-md-8">
								{!! Form::text('new_column_4',old('new_column_4'),['class' => 'form-control']) !!}
							</div>
						</div>	
						@endif

						@if($new_column_5)
						<div class="form-group">
							{!! Form::label('new_column_5','Additional column 5',['class' => 'col-md-2 control-label']) !!}
							<div class="col-md-8">
								{!! Form::text('new_column_5',old('new_column_5'),['class' => 'form-control']) !!}
							</div>
						</div>	
						@endif
						
					
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