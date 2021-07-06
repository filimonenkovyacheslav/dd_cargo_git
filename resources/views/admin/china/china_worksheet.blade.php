@extends('layouts.china_admin')
@section('content')

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
				<a href="{{ route('exportExcelChina') }}" style="margin-bottom: 20px;" class="btn btn-success btn-move">Export to Excel</a>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<!-- <div class="card-header">
						<strong class="card-title">{{ $title }}</strong>
					</div> -->

					@if (session('status') === 'Column added successfully!')
					<div class="alert alert-success">
						{{ session('status') }}
					</div>
					@elseif (session('status') === 'The quantity of columns is limited!')
					<div class="alert alert-danger">
						{{ session('status') }}
					</div>
					@elseif (session('status'))
					<div class="alert alert-success">
						{{ session('status') }}
					</div>
					@endif
					
					@can('editPost')
					<a class="btn btn-success btn-move" href="{{ route('chinaWorksheetAddColumn') }}">Add column</a>
					<a class="btn btn-primary btn-move" href="{{ route('showChinaWorksheet') }}">Add row</a>
					@endcan
					
					<div class="card-body new-worksheet">
						<div class="table-container">
							<table id="bootstrap-data-table" class="table table-striped table-bordered">
								<thead>
									<tr>
										<th>Date<hr>
											@can('china-update-post')
											<a class="btn btn-primary" target="_blank" href="{{ route('changeChinaStatusDate') }}">Change</a>
											@endcan
										</th>
										<th>Tracking number main</th> 
										<th>Local tracking number</th>
										<th>Status</th>
										<th>Customer name</th>
										<th>Customer address</th>
										<th>Customer phone number</th>
										<th>Customer email</th>
										<th>Supplier name</th>
										<th>Supplier address</th>
										<th>Supplier phone number</th>
										<th>Supplier email</th>
										<th>Shipment description</th>
										<th>Shipment weight</th>
										<th>Shipment length</th>
										<th>Shipment width</th>
										<th>Shipment height</th>
										<th>Lot number<hr>
											@can('china-update-post')
											<a class="btn btn-primary" target="_blank" href="{{ route('changeChinaStatus') }}">Change</a>
											@endcan
										</th>
										<th>Status He</th>
										<th>Status Ru</th>
																			
										@if($new_column_1)
										<th>{{$new_column_1}}<hr>
											@can('editPost')

											{!! Form::open(['url'=>route('chinaWorksheetDeleteColumn'),'onsubmit' => 'return ConfirmDelete()', 'class'=>'form-horizontal','method' => 'POST']) !!}
											{!! Form::hidden('name_column','new_column_1') !!}
											{!! Form::button('Delete',['class'=>'btn btn-danger','type'=>'submit']) !!}
											{!! Form::close() !!}

											@endcan
										</th>
										@endif
										@if($new_column_2)
										<th>{{$new_column_2}}<hr>
											@can('editPost')

											{!! Form::open(['url'=>route('chinaWorksheetDeleteColumn'),'onsubmit' => 'return ConfirmDelete()', 'class'=>'form-horizontal','method' => 'POST']) !!}
											{!! Form::hidden('name_column','new_column_2') !!}
											{!! Form::button('Delete',['class'=>'btn btn-danger','type'=>'submit']) !!}
											{!! Form::close() !!}

											@endcan
										</th>
										@endif
										@if($new_column_3)
										<th>{{$new_column_3}}<hr>
											@can('editPost')

											{!! Form::open(['url'=>route('chinaWorksheetDeleteColumn'),'onsubmit' => 'return ConfirmDelete()', 'class'=>'form-horizontal','method' => 'POST']) !!}
											{!! Form::hidden('name_column','new_column_3') !!}
											{!! Form::button('Delete',['class'=>'btn btn-danger','type'=>'submit']) !!}
											{!! Form::close() !!}

											@endcan
										</th>
										@endif
										@if($new_column_4)
										<th>{{$new_column_4}}<hr>
											@can('editPost')

											{!! Form::open(['url'=>route('chinaWorksheetDeleteColumn'),'onsubmit' => 'return ConfirmDelete()', 'class'=>'form-horizontal','method' => 'POST']) !!}
											{!! Form::hidden('name_column','new_column_4') !!}
											{!! Form::button('Delete',['class'=>'btn btn-danger','type'=>'submit']) !!}
											{!! Form::close() !!}

											@endcan
										</th>
										@endif
										@if($new_column_5)
										<th>{{$new_column_5}}<hr>
											@can('editPost')

											{!! Form::open(['url'=>route('chinaWorksheetDeleteColumn'),'onsubmit' => 'return ConfirmDelete()', 'class'=>'form-horizontal','method' => 'POST']) !!}
											{!! Form::hidden('name_column','new_column_5') !!}
											{!! Form::button('Delete',['class'=>'btn btn-danger','type'=>'submit']) !!}
											{!! Form::close() !!}

											@endcan
										</th>
										@endif
										<th>Change</th>

									</tr>

								</thead>
								<tbody>

									@if(isset($china_worksheet_obj))
									@foreach($china_worksheet_obj as $row)

									<tr>
										<td title="{{$row->date}}">
											<div class="div-3">{{$row->date}}</div>
										</td>
										<td title="{{$row->tracking_main}}">
											<div class="div-3">{{$row->tracking_main}}</div>
										</td>
										<td title="{{$row->tracking_local}}">
											<div class="div-3">{{$row->tracking_local}}</div>
										</td>
										<td title="{{$row->status}}">
											<div class="div-3">{{$row->status}}</div>
										</td>
										<td title="{{$row->customer_name}}">
											<div class="div-3">{{$row->customer_name}}</div>
										</td>
										<td title="{{$row->customer_address}}">
											<div class="div-3">{{$row->customer_address}}</div>
										</td>
										<td title="{{$row->customer_phone}}">
											<div class="div-3">{{$row->customer_phone}}</div>
										</td>
										<td title="{{$row->customer_email}}">
											<div class="div-3">{{$row->customer_email}}</div>
										</td>
										<td title="{{$row->supplier_name}}">
											<div class="div-3">{{$row->supplier_name}}</div>
										</td>
										<td title="{{$row->supplier_address}}">
											<div class="div-3">{{$row->supplier_address}}</div>
										</td>
										<td title="{{$row->supplier_phone}}">
											<div class="div-3">{{$row->supplier_phone}}</div>
										</td>
										<td title="{{$row->supplier_email}}">
											<div class="div-3">{{$row->supplier_email}}</div>
										</td>
										<td title="{{$row->shipment_description}}">
											<div class="div-3">{{$row->shipment_description}}</div>
										</td>
										<td title="{{$row->weight}}">
											<div class="div-3">{{$row->weight}}</div>
										</td>
										<td title="{{$row->length}}">
											<div class="div-3">{{$row->length}}</div>
										</td>
										<td title="{{$row->width}}">
											<div class="div-3">{{$row->width}}</div>
										</td>
										<td title="{{$row->height}}">
											<div class="div-3">{{$row->height}}</div>
										</td>
										<td title="{{$row->lot_number}}">
											<div class="div-3">{{$row->lot_number}}</div>
										</td>
										<td title="{{$row->status_he}}">
											<div class="div-3">{{$row->status_he}}</div>
										</td>
										<td title="{{$row->status_ru}}">
											<div class="div-3">{{$row->status_ru}}</div>
										</td>

										@if($new_column_1)
										<td title="{{$row->new_column_1}}">
											<div class="div1">{{$row->new_column_1}}</div>
										</td>
										@endif
										@if($new_column_2)
										<td title="{{$row->new_column_2}}">
											<div class="div1">{{$row->new_column_2}}</div>
										</td>
										@endif
										@if($new_column_3)
										<td title="{{$row->new_column_3}}">
											<div class="div1">{{$row->new_column_3}}</div>
										</td>
										@endif
										@if($new_column_4)
										<td title="{{$row->new_column_4}}">
											<div class="div1">{{$row->new_column_4}}</div>
										</td>
										@endif
										@if($new_column_5)
										<td title="{{$row->new_column_5}}">
											<div class="div1">{{$row->new_column_5}}</div>
										</td>
										@endif

										<td class="td-button">
											@can('china-update-post')
											<a class="btn btn-primary" href="{{ url('/admin/china-worksheet/'.$row->id) }}">Change</a>
											@endcan

											@can('editPost')

											{!! Form::open(['url'=>route('deleteChinaWorksheet'),'onsubmit' => 'return ConfirmDelete()', 'class'=>'form-horizontal','method' => 'POST']) !!}
											{!! Form::hidden('action',$row->id) !!}
											{!! Form::button('Delete',['class'=>'btn btn-danger','type'=>'submit']) !!}
											{!! Form::close() !!}

											@endcan
										</td> 
                                                               
									</tr>

									@endforeach
									@endif
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div><!-- .col-md-12 -->
		</div><!-- .row -->		
		
	</div><!-- .animated -->
</div><!-- .content -->

<script>

	function ConfirmDelete()
	{
		var x = confirm("Are you sure you want to delete?");
		if (x)
			return true;
		else
			return false;
	}

</script>

@endsection