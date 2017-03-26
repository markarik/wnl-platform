@extends('payment.invoices.layout')

@section('title')
	Faktura zaliczkowa
@endsection

@section('invoice-data')
	<table>
		<tr>
			<th>Faktura zaliczkowa</th>
			<th></th>
		</tr>
		<tr>
			<td>Zamówienie:</td>
			<td></td>
		</tr>
		<tr>
			<td>Metoda płatności:</td>
			<td></td>
		</tr>
		<tr>
			<td>Data wystawienia:</td>
			<td></td>
		</tr>
		<tr>
			<td>Data wpłaty:</td>
			<td></td>
		</tr>
	</table>
@endsection

@section('buyer')
	Adam Karmiński<br>
	ul. Łowiecka 69<br>
	64-100, Leszno<br>
@endsection

@section('orders-title')
	Zamówienie
@endsection

@section('orders-list')
	<tr>
		<td>1</td>
		<td>2</td>
		<td>3</td>
		<td>4</td>
		<td>5</td>
		<td>6</td>
		<td>7</td>
		<td>8</td>
	</tr>
@endsection

@section('orders-summary')
	<strong>Podsumowanie zamówienia</strong>
	<table>
		<tr>
			<th>Stawka VAT</th>
			<th>Wartość netto</th>
			<th>Kwota VAT</th>
			<th>Wartość brutto</th>
		</tr>
		<tr>
			<td>23%</td>
			<td>2</td>
			<td>3</td>
			<td>4</td>
		</tr>
		<tr>
			<td>Razem:</td>
			<td>2</td>
			<td>3</td>
			<td>4</td>
		</tr>
	</table>
@endsection

@section('advances')
	<strong>Poprzednie zaliczki</strong>
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
			<td>Razem</td>
			<td>0,00</td>
			<td>0,00</td>
		</tr>
	</table>
@endsection

@section('settlement')
	<strong>Rozliczenie według stawek</strong>
	<table>
		<tr>
			<th>Stawka VAT</th>
			<th>Wartość netto</th>
			<th>Kwota VAT</th>
			<th>Wartość brutto</th>
		</tr>
		<tr>
			<td>23%</td>
			<td>2</td>
			<td>3</td>
			<td>4</td>
		</tr>
		<tr>
			<td>Razem:</td>
			<td>2</td>
			<td>3</td>
			<td>4</td>
		</tr>
	</table>
@endsection

@section('payment-details')
	Wpłacono słownie: <br>
	Metoda płatnośći:
@endsection

@section('notes')
	Zamówienie #
@endsection

@section('summary')
	Wpłacono:
	Pozostało z zamówienia:
@endsection
