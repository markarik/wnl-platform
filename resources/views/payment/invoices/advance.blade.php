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
			<td>{{ $order['vatValue'] }}</td>
			<td>{{ $order['priceGross'] }}zł</td>
		</tr>
		<tr>
			<td><strong>Razem:</strong></td>
			<td>{{ $order['priceNet'] }}zł</td>
			<td>{{ $order['vatValue'] }}</td>
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
			<td>{{ $settlement['priceNet'] }}zł</td>
			<td>{{ $settlement['vatValue'] }}zł</td>
			<td>{{ $settlement['priceGross'] }}zł</td>
		</tr>
		<tr>
			<td><strong>Razem:</strong></td>
			<td>{{ $settlement['priceNet'] }}zł</td>
			<td>{{ $settlement['vatValue'] }}zł</td>
			<td>{{ $settlement['priceGross'] }}zł</td>
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
			<th>Brutto</th>
		</tr>
		@foreach($previousAdvances as $index => $invoice)
			<tr>
				<td>{{ $index + 1 }}</td>
				<td>{{ $invoice->full_number }}</td>
				<td>{{ $invoice->created_at->format('d-m-Y') }}</td>
				<td>{{ $invoice->corrected_amount }}zł</td>
			</tr>
		@endforeach
		<tr>
			<td class="no-border">&nbsp;</td>
			<td class="no-border">&nbsp;</td>
			<td><strong>Razem:</strong></td>
			<td>{{ $previousAdvances->sum('corrected_amount') }}zł</td>
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
	<p>Wpłacono: <strong>{{ $recentSettlement }}zł</strong></p>
	<p>Pozostało z zamówienia: <strong>{{ $remainingAmount }}zł</strong></p>
@endsection
