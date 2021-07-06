@extends('layouts.phil_ind_admin')
@section('content')

@can('eng-update-post')

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

					@if(isset($eng_draft_worksheet))				

						{!! Form::open(['url'=>route('engDraftWorksheetUpdate', ['id'=>$eng_draft_worksheet->id]), 'class'=>'form-horizontal china-worksheet-form phil-ind-update-form','method' => 'POST']) !!}

						<div class="form-group">
							{!! Form::label('direction','Direction',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('direction',$eng_draft_worksheet->direction,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('status','Status',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::select('status', array('' => '', 'Pending' => 'Pending', 'Forwarding to the warehouse in the sender country' => 'Forwarding to the warehouse in the sender country', 'At the warehouse in the sender country' => 'At the warehouse in the sender country', 'At the customs in the sender country' => 'At the customs in the sender country', 'Forwarding to the receiver country' => 'Forwarding to the receiver country', 'At the customs in the receiver country' => 'At the customs in the receiver country', 'Forwarding to the receiver' => 'Forwarding to the receiver', 'Delivered' => 'Delivered', 'Return' => 'Return', 'Box' => 'Box', 'Pick up' => 'Pick up', 'Specify' => 'Specify', 'Think' => 'Think', 'Canceled' => 'Canceled'), $eng_draft_worksheet->status,['class' => 'form-control']) !!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('tracking_main','Tracking number main',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('tracking_main',$eng_draft_worksheet->tracking_main,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('tracking_local','Local tracking number',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('tracking_local',$eng_draft_worksheet->tracking_local,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('pallet_number','Pallet number',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('pallet_number',$eng_draft_worksheet->pallet_number,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('comments_1','Comments 1',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('comments_1',$eng_draft_worksheet->comments_1,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('comments_2','Comments 2',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('comments_2',$eng_draft_worksheet->comments_2,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('shipper_name','Shipper\'s name',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('shipper_name',$eng_draft_worksheet->shipper_name,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('shipper_address','Shipper\'s address',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('shipper_address',$eng_draft_worksheet->shipper_address,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('standard_phone','Shipper\'s phone number (standard)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('standard_phone',$eng_draft_worksheet->standard_phone,['class' => 'form-control standard-phone'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('shipper_phone','Shipper\'s phone number (additionally)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('shipper_phone',$eng_draft_worksheet->shipper_phone,['class' => 'form-control'])!!}
							</div>
						</div>						

						<div class="form-group">
							{!! Form::label('shipper_id','Shipper\'s ID number',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('shipper_id',$eng_draft_worksheet->shipper_id,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('consignee_name','Consignee\'s name',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('consignee_name',$eng_draft_worksheet->consignee_name,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('consignee_address','Consignee\'s address',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('consignee_address',$eng_draft_worksheet->consignee_address,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('consignee_phone','Consignee\'s phone number',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('consignee_phone',$eng_draft_worksheet->consignee_phone,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('consignee_id','Consignee\'s ID number',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('consignee_id',$eng_draft_worksheet->consignee_id,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('shipped_items','Shipped items',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('shipped_items',$eng_draft_worksheet->shipped_items,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('shipment_val','Shipment\'s declared value',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('shipment_val',$eng_draft_worksheet->shipment_val,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('operator','Operator',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('operator',$eng_draft_worksheet->operator,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('courier','Courier',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('courier',$eng_draft_worksheet->courier,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('delivery_date_comments','Pick-up/delivery date and comments',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('delivery_date_comments',$eng_draft_worksheet->delivery_date_comments,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('weight','Weight',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('weight',$eng_draft_worksheet->weight,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('width','Width',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('width',$eng_draft_worksheet->width,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('height','Height',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('height',$eng_draft_worksheet->height,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('length','Length',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('length',$eng_draft_worksheet->length,['class' => 'form-control'])!!}
							</div>
						</div>
												
						<div class="form-group">
							{!! Form::label('volume_weight','Volume weight',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('volume_weight',$eng_draft_worksheet->volume_weight,['class' => 'form-control'])!!}
							</div>
						</div>
												
						<div class="form-group">
							{!! Form::label('lot','Lot',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('lot',$eng_draft_worksheet->lot,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('payment_date_comments','Payment date and comments',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('payment_date_comments',$eng_draft_worksheet->payment_date_comments,['class' => 'form-control'])!!}
							</div>
						</div>
												
						<div class="form-group">
							{!! Form::label('amount_payment','Amount of payment',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('amount_payment',$eng_draft_worksheet->amount_payment,['class' => 'form-control'])!!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('status_ru_disabled','Status Ru',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('status_ru_disabled',$eng_draft_worksheet->status_ru,['class' => 'form-control', 'disabled' => 'disabled'])!!}
								{!! Form::hidden('status_ru',$eng_draft_worksheet->status_ru)!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('status_he_disabled','Status He',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('status_he_disabled',$eng_draft_worksheet->status_he,['class' => 'form-control', 'disabled' => 'disabled'])!!}
								{!! Form::hidden('status_he',$eng_draft_worksheet->status_he)!!}
							</div>
						</div>																	

						<div class="form-group">
							{!! Form::label('consignee_name_customs','Consignee\'s name (for customs)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('consignee_name_customs',$eng_draft_worksheet->consignee_name_customs,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('consignee_address_customs','Consignee\'s address (for customs)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('consignee_address_customs',$eng_draft_worksheet->consignee_address_customs,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('consignee_phone_customs','Consignee\'s phone number (for customs)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('consignee_phone_customs',$eng_draft_worksheet->consignee_phone_customs,['class' => 'form-control'])!!}
							</div>
						</div>
						
						<div class="form-group">
							{!! Form::label('consignee_id_customs','Consignee\'s ID number (for customs)',['class' => 'col-md-2 control-label'])   !!}
							<div class="col-md-8">
								{!! Form::text('consignee_id_customs',$eng_draft_worksheet->consignee_id_customs,['class' => 'form-control'])!!}
							</div>
						</div>

							{!! Form::hidden('id',$eng_draft_worksheet->id)!!}

							{!! Form::hidden('date',$eng_draft_worksheet->date,['class' => 'form-control'])!!}

							{!! Form::hidden('direction',$eng_draft_worksheet->direction,['class' => 'form-control'])!!}

							{!! Form::hidden('status',$eng_draft_worksheet->status,['class' => 'form-control'])!!}

							{!! Form::hidden('tracking_main',$eng_draft_worksheet->tracking_main,['class' => 'form-control'])!!}

							{!! Form::hidden('order_number',$eng_draft_worksheet->order_number)!!}

							{!! Form::hidden('tracking_local',$eng_draft_worksheet->tracking_local,['class' => 'form-control'])!!}

							{!! Form::hidden('pallet_number',$eng_draft_worksheet->pallet_number,['class' => 'form-control'])!!}

							{!! Form::hidden('comments_1',$eng_draft_worksheet->comments_1,['class' => 'form-control'])!!}

							{!! Form::hidden('comments_2',$eng_draft_worksheet->comments_2,['class' => 'form-control'])!!}

							{!! Form::hidden('shipper_name',$eng_draft_worksheet->shipper_name,['class' => 'form-control'])!!}

							{!! Form::hidden('shipper_address',$eng_draft_worksheet->shipper_address,['class' => 'form-control'])!!}

							{!! Form::hidden('standard_phone',$eng_draft_worksheet->standard_phone,['class' => 'form-control'])!!}

							{!! Form::hidden('shipper_phone',$eng_draft_worksheet->shipper_phone,['class' => 'form-control'])!!}

							{!! Form::hidden('shipper_id',$eng_draft_worksheet->shipper_id,['class' => 'form-control'])!!}

							{!! Form::hidden('consignee_name',$eng_draft_worksheet->consignee_name,['class' => 'form-control'])!!}

							{!! Form::hidden('consignee_address',$eng_draft_worksheet->consignee_address,['class' => 'form-control'])!!}

							{!! Form::hidden('consignee_phone',$eng_draft_worksheet->consignee_phone,['class' => 'form-control'])!!}

							{!! Form::hidden('consignee_id',$eng_draft_worksheet->consignee_id,['class' => 'form-control'])!!}

							{!! Form::hidden('shipped_items',$eng_draft_worksheet->shipped_items,['class' => 'form-control'])!!}

							{!! Form::hidden('shipment_val',$eng_draft_worksheet->shipment_val,['class' => 'form-control'])!!}

							{!! Form::hidden('operator',$eng_draft_worksheet->operator,['class' => 'form-control'])!!}

							{!! Form::hidden('courier',$eng_draft_worksheet->courier,['class' => 'form-control'])!!}

							{!! Form::hidden('delivery_date_comments',$eng_draft_worksheet->delivery_date_comments,['class' => 'form-control'])!!}

							{!! Form::hidden('weight',$eng_draft_worksheet->weight,['class' => 'form-control'])!!}

							{!! Form::hidden('width',$eng_draft_worksheet->width,['class' => 'form-control'])!!}

							{!! Form::hidden('height',$eng_draft_worksheet->height,['class' => 'form-control'])!!}

							{!! Form::hidden('length',$eng_draft_worksheet->length,['class' => 'form-control'])!!}

							{!! Form::hidden('volume_weight',$eng_draft_worksheet->volume_weight,['class' => 'form-control'])!!}

							{!! Form::hidden('lot',$eng_draft_worksheet->lot,['class' => 'form-control'])!!}

							{!! Form::hidden('payment_date_comments', $eng_draft_worksheet->payment_date_comments,['class' => 'form-control'])!!}

							{!! Form::hidden('amount_payment',$eng_draft_worksheet->amount_payment,['class' => 'form-control'])!!}

							{!! Form::hidden('status_ru',$eng_draft_worksheet->status_ru,['class' => 'form-control'])!!}

							{!! Form::hidden('status_he',$eng_draft_worksheet->status_he,['class' => 'form-control'])!!}

							{!! Form::hidden('consignee_name_customs',$eng_draft_worksheet->consignee_name_customs,['class' => 'form-control'])!!}

							{!! Form::hidden('consignee_address_customs',$eng_draft_worksheet->consignee_address_customs,['class' => 'form-control'])!!}

							{!! Form::hidden('consignee_phone_customs',$eng_draft_worksheet->consignee_phone_customs,['class' => 'form-control'])!!}

							{!! Form::hidden('consignee_id_customs',$eng_draft_worksheet->consignee_id_customs,['class' => 'form-control'])!!}
					
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