@extends('payment.invoices.layout')

@section('title')
	Pro forma
@endsection

@section('invoice-data')
	<table>
		<tr>
			<th>Pro forma</th>
			<th>{{ $invoiceData['full_number'] }}</th>
		</tr>
		<tr>
			<td>Data wystawienia:</td>
			<td>{{ $invoiceData['date'] }}</td>
		</tr>
		<tr>
			<td>Termin płatności:</td>
			<td>{{ $invoiceData['payment_date'] }}</td>
		</tr>
		<tr>
			<td>Metoda płatności</td>
			<td>{{ $invoiceData['payment_method'] }}</td>
		</tr>
	</table>
@endsection

@section('buyer')
	{{ $buyer['name'] }}<br>
	{{ $buyer['address'] }}<br>
	{{ $buyer['zip'] }}, {{ $buyer['city'] }}<br>
	{{ $buyer['country'] }}<br>
	{{ $buyer['nip'] }}
@endsection

@section('orders-list')
	@foreach ($ordersList as $index => $order)
		<tr>
			<td>{{ $index + 1 }}</td>
			<td>{{ $order['product_name'] }}</td>
			<td>{{ $order['unit'] }}</td>
			<td>{{ $order['amount'] }}</td>
			<td>{{ $order['price'] }}</td>
			<td>23%</td>
			<td>{{ number_format(($order['price'] / 1.23), 2) }}</td>
			<td>{{ $order['price'] }}</td>
		</tr>
	@endforeach
@endsection

@section('notes')
	Zamówienie #{{ $notes['order_number'] }}
@endsection

@section('summary')
	Razem: {{ $summary['total'] }} PLN<br>
@endsection
