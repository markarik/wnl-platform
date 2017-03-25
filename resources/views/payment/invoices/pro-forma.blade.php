@extends('payment.invoices.layout')

@section('title')
	Pro forma
@endsection

@section('identifier')
	Pro forma
@endsection

@section('buyer')
	Bajer
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
	Summary
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
			<td>1</td>
			<td>2</td>
			<td>3</td>
			<td>4</td>
			<td>5</td>
		</tr>
	</table>
@endsection

@section('settlement')
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
	Razem: 2000,00 PLN
@endsection
