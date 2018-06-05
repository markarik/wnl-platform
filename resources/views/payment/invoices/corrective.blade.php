@extends('payment.invoices.corrective-layout')

@section('title')
	Faktura korekta
@endsection

@section('invoice-data')
	<table>
		<tr>
			<th>Faktura korygująca numer</th>
			<th>{{ $invoiceData['full_number'] }}</th>
		</tr>
		<tr>
			<td>Numer dokumentu korygowanego:</td>
			<td>{{ $correctedInvoice['number'] }}</td>
		</tr>
		<tr>
			<td>Data wystawienia dokumentu korygowanego:</td>
			<td>{{ $invoiceData['date'] }}</td>
		</tr>
		<tr>
			<td>Data wystawienia korekty:</td>
			<td>{{ $invoiceData['payment_date'] }}</td>
		</tr>
		<tr>
			<td colspan="2">
				<strong>Powód korekty: </strong>
				{{ $reason }}
			</td>
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

@section('before-orders-title')
	Zamówienie
@endsection

@section('before-orders-list')
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

@section('after-orders-title')
	Zamówienie
@endsection
@section('after-orders-list')
	@foreach ($refund ? $ordersCorrected : $ordersList as $index => $order)
		<tr>
			 L.p.
			<td>{{ $index + 1 }}</td>
			 Nazwa produktu
			<td>{{ $order['product_name'] }}</td>
			 Jednostka
			<td>{{ $order['unit'] }}</td>
			 Ilość
			<td>{{ $order['amount'] }}</td>
			 Cena brutto
			<td>{{ $order['priceGross'] }}zł</td>
			 VAT
			<td>{{ $order['vat'] }}</td>
			 Wartość netto
			<td>{{ $order['priceNet'] }}zł</td>
			 Wartość brutto
			<td>{{ $order['priceGross'] }}zł</td>
		</tr>
	@endforeach
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
			<td>{{ $summaryBefore['net'] }}zł</td>
			<td>{{ $summaryBefore['vat'] }}zł</td>
			<td>{{ $summaryBefore['gross'] }}zł</td>
		</tr>
		<tr>
			<td><strong>Razem:</strong></td>
			<td>{{ $summaryBefore['net'] }}zł</td>
			<td>{{ $summaryBefore['vat'] }}zł</td>
			<td>{{ $summaryBefore['gross'] }}zł</td>
		</tr>
	</table>
@endsection

@section('afterCorrectionSettlement')
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
			<td>{{ $summaryAfter['net'] }}zł</td>
			<td>{{ $summaryAfter['vat'] }}zł</td>
			<td>{{ $summaryAfter['gross'] }}zł</td>
		</tr>
		<tr>
			<td><strong>Razem:</strong></td>
			<td>{{ $summaryAfter['net'] }}zł</td>
			<td>{{ $summaryAfter['vat'] }}zł</td>
			<td>{{ $summaryAfter['gross'] }}zł</td>
		</tr>
	</table>
@endsection

@section('taxDifference')
	@if($taxDifference['vat'] < 0)
		<h4>Zmniejszenie podatku należnego</h4>
	@else
		<h4>Zwiększenie podatku należnego</h4>
	@endif
	<table>
		<tr>
			<th>Stawka VAT</th>
			<th>Wartość netto</th>
			<th>Kwota VAT</th>
			<th>Wartość brutto</th>
		</tr>
		<tr>
			<td>{{ $order['vat'] }}</td>
			<td>{{ $taxDifference['net'] }}zł</td>
			<td>{{ $taxDifference['vat'] }}zł</td>
			<td>{{ $taxDifference['gross'] }}zł</td>
		</tr>
		<tr>
			<td><strong>Razem:</strong></td>
			<td>{{ $taxDifference['net'] }}zł</td>
			<td>{{ $taxDifference['vat'] }}zł</td>
			<td>{{ $taxDifference['gross'] }}zł</td>
		</tr>
	</table>
@endsection


{{--
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
				<td>{{ $invoice->amount }}zł</td>
			</tr>
		@endforeach
		<tr>
			<td class="no-border">&nbsp;</td>
			<td class="no-border">&nbsp;</td>
			<td><strong>Razem:</strong></td>
			<td>{{ $previousAdvances->sum('amount') }}zł</td>
		</tr>
	</table>
@endsection
--}}

@section('notes')
	<ul>
		@foreach ($notes as $note)
			<li>{{ $note }}</li>
		@endforeach
	</ul>
@endsection

@section('summary')
	<p>Metoda płatności: <strong>{{ $invoiceData['payment_method'] }}</strong></p>
	<p>Wpłacono: <strong>{{ $paid }}zł</strong></p>
	<p>Pozostało z zamówienia: <strong>{{ $remainingAmountBefore }}zł</strong></p>
@endsection

@section('afterCorrectionSummary')
	<p>Metoda płatności: <strong>{{ $invoiceData['payment_method'] }}</strong></p>
	@if (!$refund)
		<p>Wpłacono: <strong>{{ $paid + $difference }}zł</strong></p>
		<p>Pozostało z zamówienia: <strong>{{ $remainingAmount }}zł</strong></p>
	@else
		<p>Wpłacono: <strong>{{ $paid }}zł</strong></p>
		<p style="font-size: small">
			@if($difference < 0)
				<strong>Do zwrotu: {{ $difference * -1 }}zł</strong>
			@else
				<strong>Do zapłaty: {{ $difference }}zł</strong>
			@endif
		</p>
	@endif
@endsection
