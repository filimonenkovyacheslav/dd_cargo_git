@extends('layouts.phil_ind_admin')
@section('content')

@can('editColumns-eng')

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

					@if(isset($packing_eng))				

						{!! Form::open(['url'=>route('updatePackingEng', ['id'=>$packing_eng->id]), 'class'=>'form-horizontal','method' => 'POST']) !!}

						<div class="form-group">
							{!! Form::label('tracking','Tracking Number',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('tracking',$packing_eng->tracking,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('country','Destination Country',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('country',$packing_eng->country,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('shipper_name','Shipper name',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('shipper_name',$packing_eng->shipper_name,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('shipper_address','Shipper address',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('shipper_address',$packing_eng->shipper_address,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('shipper_phone','Shipper Phone No.',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('shipper_phone',$packing_eng->shipper_phone,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('shipper_id','Shipper ID No.',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('shipper_id',$packing_eng->shipper_id,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('consignee_name','Consignee name',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('consignee_name',$packing_eng->consignee_name,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('consignee_address','Consignee address',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('consignee_address',$packing_eng->consignee_address,['class' => 'form-control'])!!}
							</div>
						</div>						

						<div class="form-group">
							{!! Form::label('consignee_phone','Consignee Phone No.',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('consignee_phone',$packing_eng->consignee_phone,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('consignee_id','Consignee ID No.',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('consignee_id',$packing_eng->consignee_id,['class' => 'form-control'])!!}
							</div>
						</div>												

						<div class="form-group">
							{!! Form::label('length','Dimensions (length)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('length',$packing_eng->length,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('width','Dimensions (width)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('width',$packing_eng->width,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('height','Dimensions (height)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('height',$packing_eng->height,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('weight','Weight',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('weight',$packing_eng->weight,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('items','Items enclosed',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('items',$packing_eng->items,['class' => 'form-control'])!!}
							</div>
						</div>	

						<div class="form-group">
							{!! Form::label('shipment_val','Declared Value',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('shipment_val',$packing_eng->shipment_val,['class' => 'form-control'])!!}
							</div>
						</div>	

						{!! Form::hidden('work_sheet_id',$packing_eng->work_sheet_id) !!}				
					
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