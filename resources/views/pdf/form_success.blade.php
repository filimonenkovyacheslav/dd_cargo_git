@extends('layouts.front_signature_form')

@section('content')

<center>

@if (isset($_GET['new_document_id']))
<div class="alert alert-success  alert-dismissible">
    <button type="button" class="close" data-dismiss="alert">×</button>  
    <strong>File {{ $_GET['pdf_file'] }} signed and saved successfully</strong>
    <a href="{{ url('/download-pdf/'.$_GET['new_document_id']) }}">Download PDF</a>
    @if (isset($_GET['old_file']))
    <strong>Old document file {{ $_GET['old_file'] }}</strong>
    @endif
</div>
@endif

</center>

@endsection




