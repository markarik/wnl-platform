<?php /** @var Closure $n */ ?>
@extends('payment.invoices.final-layout')

@section('title')
	Faktura zaliczkowa
@endsection

@section('invoice-data')
	<table>
		<tr>
			<th>Faktura końcowa</th>
			<th>{{ $invoiceData['full_number'] }}</th>
		</tr>
		<tr>
			<td>Data wystawienia:</td>
			<td>{{ $invoiceData['date'] }}</td>
		</tr>
		{{--<tr>--}}
		{{--<td>Data wpłaty:</td>--}}
		{{--<td>{{ $invoiceData['payment_date'] }}</td>--}}
		{{--</tr>--}}
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
			{{-- Wartość brutto --}}
			<td>{{ $order['priceGross'] }}zł</td>
		</tr>
	@endforeach
@endsection

@section('orders-summary')
	<h4>Podsumowanie zamówienia</h4>
	<table>
		<tr>
			<th class="no-border">&nbsp;</th>
			<th>Wartość netto</th>
			<th>Kwota VAT</th>
			<th>Wartość brutto</th>
		</tr>
		<tr>
			<td><strong>Razem:</strong></td>
			<td>{{ $summary['net'] }}zł</td>
			<td>{{ $summary['vat'] }}zł</td>
			<td>{{ $summary['total'] }}zł</td>
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
		@foreach($vatSummary as $rate => $summary)
			<tr>
				<td>{{ $rate }}</td>
				<td>{{ $n($summary['net']) }}zł</td>
				<td>{{ $n($summary['vat']) }}zł</td>
				<td>{{ $n($summary['gross']) }}zł</td>
			</tr>
		@endforeach
		<tr>
			<td><strong>Razem:</strong></td>
			<td>{{ $n($vatSummaryTotal['net']) }}zł</td>
			<td>{{ $n($vatSummaryTotal['vat']) }}zł</td>
			<td>{{ $n($vatSummaryTotal['gross']) }}zł</td>
		</tr>
	</table>
@endsection

@section('final-advances')
	<h4>Zaliczki</h4>
	<table>
		<tr>
			<th>Lp</th>
			<th>Numer faktury</th>
			<th>Data</th>
			<th>Stawka VAT</th>
			<th>Netto</th>
			<th>Kwota VAT</th>
			<th>Brutto</th>
		</tr>
		@foreach($previousAdvances as $index => $invoice)
			<tr>
				<td>{{ $index + 1 }}</td>
				<td>{{ $invoice->full_number }}</td>
				<td>{{ $invoice->created_at->format('d-m-Y') }}</td>
				<td>{{ $invoice->vat }}</td>
				<td>{{ number_format($invoice->vat === 'zw' ? $invoice->amount : $invoice->amount / 1.23,  2, ',', ' ')}}
					zł
				</td>
				<td>{{ number_format($invoice->vat === 'zw' ? 0 : $invoice->amount - $invoice->amount / 1.23,  2, ',', ' ')}}
					zł
				</td>
				<td>{{ number_format($invoice->amount, 2, ',', ' ') }}zł</td>
			</tr>
		@endforeach
		<tr>
			<td class="no-border">&nbsp;</td>
			<td class="no-border">&nbsp;</td>
			<td class="no-border">&nbsp;</td>
			<td class="no-border">&nbsp;</td>
			<td class="no-border">&nbsp;</td>
			<td><strong>Razem:</strong></td>
			<td>{{ $previousAdvances->sum('amount') }}zł</td>
		</tr>
	</table>
@endsection

@section('instalments')
	@if($invoiceOrder->method === 'instalments' && !$invoiceOrder->instalments['allPaid'])
		<h4>Terminy kolejnych rat</h4>
		<table>
			<tr>
				<th>Termin płatności</th>
				<th>Netto</th>
				<th>Vat</th>
				<th>Brutto</th>
			</tr>
			<?php /** @var \App\Models\OrderInstalment $instalment */ ?>
			@foreach($invoiceOrder->instalments['instalments'] as $index => $instalment)
				@if($instalment->left_amount > 0)
					<tr>
						<td>{{ $instalment->due_date->format('d-m-Y') }}</td>
						<td>{{ $n($instalment->amount / 1.23) }}zł</td>
						<td>{{ $n($instalment->amount - $instalment->amount / 1.23) }}zł</td>
						<td>{{ $n($instalment->amount) }}zł</td>
					</tr>
				@endif
			@endforeach
			<tr>
				<td><strong>Razem</strong></td>
				<td>{{ $n($invoiceOrder->instalments['total'] / 1.23) }}zł</td>
				<td>{{ $n($invoiceOrder->instalments['total'] - $invoiceOrder->instalments['total'] / 1.23) }}zł</td>
				<td>{{ $n($invoiceOrder->instalments['total']) }}zł</td>
			</tr>
		</table>
	@endif
@endsection

@section('notes')
	<ul>
		@foreach ($notes as $note)
			<li>{{ $note }}</li>
		@endforeach
	</ul>
@endsection

@section('summary')
	<p>
	Metoda płatności: <strong>{{ $invoiceData['payment_method'] }}</strong>
	<br/>
	Wpłacono: <strong>{{ $previousAdvances->sum('amount') }}zł</strong>
	<br/>
	Pozostało do zapłaty: <strong>{{ $remainingAmount }}zł</strong>
	</p>
	@if($remainingAmount > 0)
		<p>
		<small><strong>Uwaga!</strong> Dla wpłat dokonanych po {{ $invoiceData['date'] }} nie możemy wystawiać już kolejnych faktur i uwzględniać ich na fakturze końcowej. Co nie znaczy, że do nas nie dotarły. ;) Ubiegając się o refundację załącz do tej faktury potwierdzenia przelewów pokrywających brakującą kwotę. :) Stan swoich zamówień znajdziesz w zakładce KONTO > Twoje zamówienia.</small>
		</p>
	@endif
		
@endsection
