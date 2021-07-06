@extends('layouts.phil_ind_admin')
@section('content')

@can('eng-update-post')
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

						{!! Form::open(['url'=>route('philIndWorksheetAdd'),'onsubmit' => 'сonfirmSigned(event)', 'class'=>'form-horizontal china-worksheet-form','method' => 'POST']) !!}

						@can('editColumns-eng')

						{!! Form::hidden('date',date("Y.m.d"))!!}

						<div class="form-group">
							{!! Form::label('direction','Direction',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('direction',old('direction'),['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('status','Status',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::select('status', array('' => '', 'Pending' => 'Pending', 'Forwarding to the warehouse in the sender country' => 'Forwarding to the warehouse in the sender country', 'At the warehouse in the sender country' => 'At the warehouse in the sender country', 'At the customs in the sender country' => 'At the customs in the sender country', 'Forwarding to the receiver country' => 'Forwarding to the receiver country', 'At the customs in the receiver country' => 'At the customs in the receiver country', 'Forwarding to the receiver' => 'Forwarding to the receiver', 'Delivered' => 'Delivered', 'Return' => 'Return', 'Box' => 'Box', 'Pick up' => 'Pick up', 'Specify' => 'Specify', 'Think' => 'Think', 'Canceled' => 'Canceled'), '',['class' => 'form-control']) !!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('tracking_main','Main Tracking number',['class' => 'col-md-2 control-label'])   !!}
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

						@endcan

						<div class="form-group">
							{!! Form::label('pallet_number','Pallet number',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('pallet_number',old('pallet_number'),['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('comments_1','Comments 1',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('comments_1',old('comments_1'),['class' => 'form-control'])!!}
							</div>
						</div>

						@can('update-user')

						<div class="form-group">
							{!! Form::label('comments_2','Comments 2',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('comments_2',old('comments_2'),['class' => 'form-control'])!!}
							</div>
						</div>	

						@endcan	

						@can('editColumns-eng')										
						
						<div class="form-group">
							{!! Form::label('shipper_name','Shipper\'s name',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('shipper_name',old('shipper_name'),['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('shipper_address','Shipper\'s address',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('shipper_address',old('shipper_address'),['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('standard_phone','Shipper\'s phone number (standard)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('standard_phone',old('standard_phone'),['class' => 'form-control standard-phone'])!!}
							</div>
						</div>	
						
						<div class="form-group">
							{!! Form::label('shipper_phone','Shipper\'s phone number (additionally)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('shipper_phone',old('shipper_phone'),['class' => 'form-control'])!!}
							</div>
						</div>						

						<div class="form-group">
							{!! Form::label('shipper_id','Shipper\'s ID number',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('shipper_id',old('shipper_id'),['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('consignee_name','Consignee\'s name',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('consignee_name',old('consignee_name'),['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('consignee_address','Consignee\'s address',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('consignee_address',old('consignee_address'),['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('consignee_phone','Consignee\'s phone number',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('consignee_phone',old('consignee_phone'),['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('consignee_id','Consignee\'s ID number',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('consignee_id',old('consignee_id'),['class' => 'form-control'])!!}
							</div>
						</div>

						@endcan
						
						<div class="form-group">
							{!! Form::label('shipped_items','Shipped items',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('shipped_items',old('shipped_items'),['class' => 'form-control'])!!}
							</div>
						</div>

						@can('editColumns-eng')

						<div class="form-group">
							{!! Form::label('shipment_val','Shipment\'s declared value',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('shipment_val',old('shipment_val'),['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('operator','Operator',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('operator',old('operator'),['class' => 'form-control'])!!}
							</div>
						</div>

						@endcan

						@can('editPost')

						<div class="form-group">
							{!! Form::label('courier','Courier',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('courier',old('courier'),['class' => 'form-control'])!!}
							</div>
						</div>

						@endcan

						@can('editColumns-eng')

						<div class="form-group">
							{!! Form::label('delivery_date_comments','Pick-up/delivery date and comments',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('delivery_date_comments',old('delivery_date_comments'),['class' => 'form-control'])!!}
							</div>
						</div>

						@endcan

						@can('editColumns-2')
						
						<div class="form-group">
							{!! Form::label('weight','Weight',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('weight',old('weight'),['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('width','Width',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('width',old('width'),['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('height','Height',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('height',old('height'),['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('length','Length',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('length',old('length'),['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('volume_weight','Volume weight',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('volume_weight',old('volume_weight'),['class' => 'form-control'])!!}
							</div>
						</div>

						@endcan

						@can('editPost')
																													
						<div class="form-group">
							{!! Form::label('lot','Lot',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('lot',old('lot'),['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('payment_date_comments','Payment date and comments',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('payment_date_comments',old('payment_date_comments'),['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('amount_payment','Amount of payment',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('amount_payment',old('amount_payment'),['class' => 'form-control'])!!}
							</div>
						</div>

						@endcan

						<div class="form-group">
							{!! Form::label('status_ru_disabled','Status Ru',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('status_ru_disabled',old('status_ru'),['class' => 'form-control', 'disabled' => 'disabled'])!!}
								{!! Form::hidden('status_ru',old('status_ru'))!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('status_he_disabled','Status He',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('status_he_disabled',old('status_he'),['class' => 'form-control', 'disabled' => 'disabled'])!!}
								{!! Form::hidden('status_he',old('status_he'))!!}
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

						@can('editColumns-eng')

						<div class="form-group">
							{!! Form::label('consignee_name_customs','Consignee\'s name (for customs)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('consignee_name_customs',old('consignee_name_customs'),['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('consignee_address_customs','Consignee\'s address (for customs)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('consignee_address_customs',old('consignee_address_customs'),['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('consignee_phone_customs','Consignee\'s phone number (for customs)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('consignee_phone_customs',old('consignee_phone_customs'),['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('consignee_id_customs','Consignee\'s ID number (for customs)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('consignee_id_customs',old('consignee_id_customs'),['class' => 'form-control'])!!}
							</div>
						</div>

						@endcan						
					
						{!! Form::button('Save',['class'=>'btn btn-primary','type'=>'submit']) !!}
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
            alert('The number of characters in the phone must be from 10 to 13 !');
            return false;
        }

        form.submit();
    }
</script>

@else
<h1>You cannot view this page!</h1>
@endcan 
@endsection