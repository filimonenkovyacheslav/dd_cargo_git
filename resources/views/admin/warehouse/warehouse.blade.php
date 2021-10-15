@extends('layouts.admin')
@section('content')
@can('editColumns-2')
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
				<a href="{{ route('exportExcelWarehouse') }}" style="margin-bottom: 20px;" class="btn btn-success btn-move">Export to Excel</a>
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
					
					<a class="btn" id="open-modal" data-toggle="modal" data-target="#warehouseOpenModal"></a>

					<div class="btn-move-wrapper" style="display:flex">
						<form action="{{ url('/admin/warehouse-filter/') }}" method="GET" id="form-worksheet-table-filter" enctype="multipart/form-data">
							@csrf
							<label class="table_columns" style="margin: 0 15px">Choose column:
								<select class="form-control" id="table_columns" name="table_columns">
									<option value="" selected="selected"></option>
									<option value="pallet">PALLET</option>
									<option value="cell">CELL</option>
									<option value="arrived">ARRIVED</option>
									<option value="left">LEFT</option>
									<option value="lot">LOT</option>			
									<option value="notifications">NOTIFICATIONS</option>              
								</select>
							</label>
							<label>Filter:
								<input type="search" name="table_filter_value" class="form-control form-control-sm">
							</label>
							<button type="button" id="table_filter_button" style="margin-left:30px" class="btn btn-default">Search</button>
						</form>
					</div>
					
					<div class="card-body new-worksheet">
						<div class="table-container">
							<table class="table table-striped table-bordered">
								<thead>
									<tr>
										<th>Change</th>
										<th>PALLET</th>
										<th>CELL</th>
										<th>ARRIVED</th>
										<th>LEFT</th>
										<th>LOT</th>
										<th>NOTIFICATIONS</th>
									</tr>

								</thead>
								<tbody>

									@if($warehouse_obj->count())
									@foreach($warehouse_obj as $row)

									<tr>
										<td class="td-button">
											<a class="btn btn-primary" href="{{ url('/admin/warehouse-update/'.$row->id) }}">Edit</a>

											<a class="btn btn-success" onclick="openModal(event)" href="{{ url('/admin/warehouse-open/'.$row->id) }}">Open</a>

											{!! Form::open(['url'=>route('deleteWarehouse'),'onsubmit' => 'return ConfirmDelete()', 'class'=>'form-horizontal','method' => 'POST']) !!}
											{!! Form::hidden('action',$row->id) !!}
											{!! Form::button('Delete',['class'=>'btn btn-danger','type'=>'submit']) !!}
											{!! Form::close() !!}

										</td>
										<td title="{{$row->pallet}}">
											<div class="div-3">{{$row->pallet}}</div>
										</td>
										<td title="{{$row->cell}}">
											<div class="div-3">{{$row->cell}}</div>
										</td>
										<td title="{{$row->arrived}}">
											<div class="div-3">{{$row->arrived}}</div>
										</td>
										<td title="{{$row->left}}">
											<div class="div-3">{{$row->left}}</div>
										</td>
										<td title="{{$row->lot}}">
											<div class="div-3">{{$row->lot}}</div>
										</td>										
										<td title="{{$row->notifications}}">
											<div style="width: 400px">{{$row->notifications}}</div>
										</td>                  
									</tr>

									@endforeach
									@endif
								</tbody>
							</table>

							@if(isset($data))
							{{ $warehouse_obj->appends($data)->links() }}
							@else
							{{ $warehouse_obj->links() }}
							@endif
						
						</div>
					</div>
				</div>
			</div><!-- .col-md-12 -->
		</div><!-- .row -->		
		
	</div><!-- .animated -->
</div><!-- .content -->

<!-- Modal -->
<div class="modal fade" id="warehouseOpenModal" tabindex="-1" role="dialog" aria-labelledby="warehouseOpenModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="warehouseOpenModalLabel">Add tracking number</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form action="" method="POST" enctype="multipart/form-data">
				@csrf
				<div class="modal-body">
																		
				</div>
			</form>
		</div>
	</div>
</div>

<script>

	function ConfirmDelete()
	{
		var x = confirm("Are you sure you want to delete?");
		if (x)
			return true;
		else
			return false;
	}

	function openModal(event)
	{
		event.preventDefault();
		const href = event.target.href;
		console.log(href);
		document.querySelector('#open-modal').click();
	}

	/*function sumByDate(){		
		let fromDate = document.querySelector('[name="from_date"]').value;
		let toDate = document.querySelector('[name="to_date"]').value;

		if (fromDate && toDate) {
			fromDate = fromDate.split('-');
			fromDate = fromDate[0].slice(2)+fromDate[1]+fromDate[2];
			toDate = toDate.split('-');
			toDate = toDate[0].slice(2)+toDate[1]+toDate[2];
			
			$.ajax({
				url: "{{ url('/admin/receipts-sum/') }}"+"?from_date="+fromDate+"&to_date="+toDate,
				type: "GET",
				headers: {
					'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
				},
				success: function (data) {
					$('.receipts-sum .alert').remove()
					data = JSON.parse(data)
					if (data.error) {
						$('.receipts-sum button').after('<div class="alert alert-danger">'+data.error+'</div>')
					}
					if (data.sum) {
						$('.receipts-sum button').after('<div class="alert alert-success">Сумма: '+data.sum+'</div>')
					}
				},
				error: function (msg) {
					alert('Ошибка admin');
				}
			});
		}
	}*/

</script>
@else
<h1>You cannot view this page!</h1>
@endcan
@endsection