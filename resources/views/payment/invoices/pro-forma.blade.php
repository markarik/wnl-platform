@extends('payment.invoices.layout')

@section('title')
	Pro forma
@endsection

@section('identifier')
	<table>
		<tr>
			<th>Pro forma</th>
			<th></th>
		</tr>
		<tr>
			<td>Data wystawienia:</td>
			<td></td>
		</tr>
		<tr>
			<td>Termin płatności:</td>
			<td></td>
		</tr>
		<tr>
			<td>Metoda płatności</td>
			<td></td>
		</tr>
	</table>
@endsection

@section('buyer')
	Adam Karmiński<br>
	ul. Łowiecka 69<br>
	64-100, Leszno<br>
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

@section('notes')
	Zamówienie #1
@endsection

@section('summary')
	Razem: 2000,00 PLN<br>
	Słownie:
@endsection
