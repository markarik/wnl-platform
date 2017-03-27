@extends('payment.invoices.layout')

@section('title')
	Faktura zaliczkowa
@endsection

@section('invoice-data')
	<table>
		<tr>
			<th>Faktura zaliczkowa</th>
			<th>{{ $invoiceData['full_number'] }}</th>
		</tr>
		<tr>
			<td>Data wystawienia:</td>
			<td>{{ $invoiceData['date'] }}</td>
		</tr>
		<tr>
			<td>Data wpłaty:</td>
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

@section('orders-title')
	Zamówienie
@endsection

@section('orders-list')
	@foreach ($ordersList as $index => $order)
		<tr>
			{{-- L.p. --}}
			<td>{{ $index + 1 }}</td>
			{{-- Nazwa produktu --}}
			<td>{{ $order['product_name'] }}</td>
			{{-- Jednostka --}}
			<td>{{ $order['unit'] }}</td>
			{{-- Ilość --}}
			<td>{{ $order['amount'] }}</td>
			{{-- Cena brutto --}}
			<td>{{ $order['priceGross'] }}zł</td>
			{{-- VAT --}}
			<td>{{ $order['vat'] }}</td>
			{{-- Wartość netto --}}
			<td>{{ $order['priceNet'] }}zł</td>
			{{-- Wartość brutto --}}
			<td>{{ $order['priceGross'] }}zł</td>
		</tr>
	@endforeach
@endsection

@section('orders-summary')
	<h4>Podsumowanie zamówienia</h4>
	<table>
		<tr>
			<th>Stawka VAT</th>
			<th>Wartość netto</th>
			<th>Kwota VAT</th>
			<th>Wartość brutto</th>
		</tr>
		<tr>
			<td>{{ $order['vat'] }}</td>
			<td>{{ $order['priceNet'] }}zł</td>
			<td>{{ $order['vatValue'] }}zł</td>
			<td>{{ $order['priceGross'] }}zł</td>
		</tr>
		<tr>
			<td><strong>Razem:</strong></td>
			<td>{{ $order['priceNet'] }}zł</td>
			<td>{{ $order['vatValue'] }}zł</td>
			<td>{{ $order['priceGross'] }}zł</td>
		</tr>
	</table>
@endsection

@section('settlement')
	<h4>Rozliczenie wg stawek</h4>
	<table>
		<tr>
			<th>Stawka VAT</th>
			<th>Wartość netto</th>
			<th>Kwota VAT</th>
			<th>Wartość brutto</th>
		</tr>
		<tr>
			<td>{{ $order['vat'] }}</td>
			<td>{{ $order['priceNet'] }}zł</td>
			<td>{{ $order['vatValue'] }}zł</td>
			<td>{{ $order['priceGross'] }}zł</td>
		</tr>
		<tr>
			<td><strong>Razem:</strong></td>
			<td>{{ $order['priceNet'] }}zł</td>
			<td>{{ $order['vatValue'] }}zł</td>
			<td>{{ $order['priceGross'] }}zł</td>
		</tr>
	</table>
@endsection

@section('advances')
	<h4>Poprzednie zaliczki</h4>
	<table>
		<tr>
			<th>Lp</th>
			<th>Numer faktury</th>
			<th>Data</th>
			<th>Netto</th>
			<th>Brutto</th>
		</tr>
		<tr>
			<td class="hidden"></td>
			<td class="hidden"></td>
			<td>Razem:</td>
			<td>0,00</td>
			<td>0,00</td>
		</tr>
	</table>
@endsection

@section('notes')
	<ul>
	@foreach ($notes as $note)
		<li>{{ $note }}</li>
	@endforeach
	</ul>
@endsection

@section('summary')
	<p>Metoda płatności: <strong>{{ $invoiceData['payment_method'] }}</strong></p>
	<p>Wpłacono: <strong>{{ $order['priceGross'] }}zł</strong></p>
	<p>Pozostało z zamówienia: 0.00zł</strong></p>
@endsection
