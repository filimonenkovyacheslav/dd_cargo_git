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

					<div class="card-body">	
						@if($article !== null)
						<form action="{{ route('updateFrontPage', ['id'=>$article->id]) }}" method="POST">
							{{ csrf_field() }}
							<label for="title">Заголовок</label><br>
							<input type="text" name="title" id="title" value="{{ $article->title }}"><br>
							<label for="urn">URN</label><br>
							<input type="text" name="urn" id="urn" value="{{ $article->urn }}"><br>
							<label for="text">Текст</label><br>
							<textarea id="text" name="editor1"></textarea><br>
							<button type="submit">Изменить</button>
						</form>
						@endif											        
                    </div>
                
                </div>
            </div>


        </div>
    </div><!-- .animated -->
</div><!-- .content -->
<script>
	CKEDITOR.replace( 'editor1', {
		filebrowserUploadUrl: "{{route('upload', ['_token' => csrf_token() ])}}",
		filebrowserUploadMethod: 'form'
	} );
</script>
@endsection