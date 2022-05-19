<style type="text/css">
	td {
		min-width: 100px;
	}
	table{
		width: 600px;
		text-align: left;
	}
	td img{
		width:120px;
		height:70px;
	}
</style>
<center>

	<h2>{{ $document->uniq_id }}</h2>
	@if ($cancel)
	<h1 style="color:red">CANCELED</h1>
	@endif
	
	<table>		
		<tr>			
			<th><img src="{{ asset('/images/tracking.png') }}"></th>
			<td>{{ $tracking }}</td>
			<td></td>
			<th><img src="{{ asset('/images/weight.png') }}"></th>
			<td>{{ $worksheet->weight }}</td>
		</tr>
		<tr>			
			<th><img src="{{ asset('/images/date.png') }}"></th>
			<td>{{ $document->date }}</td>
			<td></td>
			<th><img src="{{ asset('/images/dimensions.png') }}"></th>
			<td>{{ $worksheet->width}}x{{$worksheet->height}}x{{$worksheet->length }}</td>
		</tr>
	</table>	

	<img src="{{ asset('/upload/ru_forms/'.$document->screen_ru_form) }}" style="width:600px;height:800px">	
	
	<table>		
		<tr>			
			<th><img src="{{ asset('/images/sender_name.png') }}"></th>
			<td>{{ $worksheet->sender_name }}</td>
			<td></td>
			<th><img src="{{ asset('/images/sender_signature.png') }}"></th>
			<td><img src="{{ asset('/upload/signatures/'.$document->signature) }}"></td>
		</tr>
	</table>
</center>








