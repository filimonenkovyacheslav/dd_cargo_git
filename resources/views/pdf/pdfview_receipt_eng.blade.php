<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo&display=swap" rel="stylesheet">
    <title>Document</title>
</head>
<body style="font-family: 'Cairo', sans-serif;">

<style type="text/css">
	td,th {
		min-width: 100px;
		border: 1px solid black;
		padding: 3px;
		text-align: right;
	}
	table{
		font-size: 10px;
		border-collapse: collapse;
		border: 2px solid black;		
		vertical-align: middle;
		width:400px;
	}
</style>
<center dir="rtl">
	
	<table>	
		<tr>
			<th colspan="2" style="font-size:14px">
				קבלה זמנית
			</th>
		</tr>	
		<tr>
			<th colspan="2" style="text-align: center;">
				 הראשונים 13 פתח תקווה Oriental Express 
			</th>
		</tr>
		<tr>			
			<th>נתקבל מ</th>
			<td>{{ $receipt['senderName'] }}</td>
		</tr>
		<tr>			
			<th>נתובת</th>
			<td></td>
		</tr>		
		<tr>			
			<th>מספר משלוחים</th>
			<td>{{ $receipt['quantity'] }}</td>
		</tr>
		<tr>			
			<th>סְכוּם</th>
			<td>{{ $receipt['amount'] }}</td>
		</tr>
		<tr>			
			<th>תַאֲרִיך</th>
			<td>{{ $date }}</td>
		</tr>

	</table>
</center>

</body>
</html>








