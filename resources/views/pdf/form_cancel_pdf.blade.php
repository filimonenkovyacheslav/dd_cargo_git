@extends('layouts.front_signature_form')

@section('content')

@php
if($type === 'draft_id' || $type === 'worksheet_id') $ru = true;
else $ru = false;
@endphp

<center>

{!! Form::open(['url'=>route('signatureForCancel'), 'class'=>'form-horizontal','method' => 'GET']) !!}

{!! Form::hidden('type',$type) !!}
{!! Form::hidden('id',$worksheet->id) !!}

@if($ru)
<h4 style="margin: 50px auto; width: 300px">
	<img src="{{ asset('/images/cancel_img_1.png') }}">
{{ $worksheet->getLastDocUniq() }}
@if($worksheet->tracking_main)
<img src="{{ asset('/images/cancel_img_2.png') }}">
{{ $worksheet->tracking_main }}
@endif
</h4>
<br>
<label for="create_new">ЗАПОЛНИТЬ ЗАНОВО</label>
@elseif(!$ru)
<h4 style="margin: 50px auto; width: 300px">{{ $message }}</h4>
<br>
<label for="create_new">FILL AGAIN</label>
@endif

<input type="checkbox" id="create_new" name="create_new">
<br>

{!! Form::button( ($ru) ? 'Подписать' : 'To sign',['class'=>'btn btn-primary','type'=>'submit']) !!}
{!! Form::close() !!} 

</center>

@endsection




