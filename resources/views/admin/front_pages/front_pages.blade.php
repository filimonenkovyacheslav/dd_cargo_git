@extends('layouts.admin')
@section('content')

<div class="content mt-3">
	<div class="animated fadeIn">
		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-header">
						<strong class="card-title">{{ $title }}</strong>
					</div>

					@if (session('status'))
					<div class="alert alert-success">
						{{ session('status') }}
					</div>
					@endif

					@can('update-user')
					<a class="btn btn-primary btn-move" href="{{ route('addFrontPage') }}">Добавить страницу</a>
					@endcan 

					<div class="card-body">	
						<table id="bootstrap-data-table" class="table table-striped table-bordered">
							<thead>
								<tr>
									<th>Название</th>
									<th>URN</th>
									@can('update-user')
									<th>Изменить</th>
									@endcan 
								</tr>
							</thead>
							<tbody>
								
								@if(isset($articles))
								@foreach($articles as $article)
								
								<tr>
									<td>{{$article->title}}</td>
									<td>{{$article->urn}}</td>
                                    
                                    @can('update-user')
                                    <td>
                                    	<a class="btn btn-primary" href="{{ url('/admin/update-front-page/'.$article->id) }}">Изменить</a>
                                    	{!! Form::open(['url'=>route('deleteFrontPage'),'onsubmit' => 'return ConfirmDelete()', 'class'=>'form-horizontal','method' => 'POST']) !!}
                                    	{!! Form::hidden('action',$article->id) !!}
                                    	{!! Form::button('Удалить',['class'=>'btn btn-danger','type'=>'submit']) !!}
                                    	{!! Form::close() !!}
                                    </td>
                                    @endcan 
                                
                                </tr>

                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


        </div>
    </div><!-- .animated -->
</div><!-- .content -->

<script>

	function ConfirmDelete()
	{
		var x = confirm("Вы уверены, что хотите удалить?");
		if (x)
			return true;
		else
			return false;
	}

</script>

@endsection