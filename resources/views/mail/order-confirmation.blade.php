@extends('mail.layout')

@section('content')
	<h3>Cześć {{ $order->user->first_name or '{Adam}' }}, witaj ponownie!</h3>

	<p>Piszemy do Ciebie, żeby potwierdzić zamówienie numer {{ $order->id or '{12345}' }} złożone na kurs <strong>{{$order->product->name or '{Kurs internetowy}'}}</strong>.</p>

	<p>W załączniku znajdziesz dokument pro forma, który zawiera szczegóły zamówienia. Stanowi on jednocześnie podstawę do płatności.</p>

	<p>Jeżeli wybierasz płatność przelewem bankowym, wykonaj go przy użyciu poniższych danych. :)</p>

	<h4>Dane do przelewu</h4>
	<table style="font-size: 0.9em; line-height: 2em;">
		<tr>
			<td style="text-align: right; padding-right: 15px;">Tytuł przelewu:</td>
			<td><strong>Zamówienie numer {{ $order->id or '{12345}' }}</strong></td>
		</tr>
		<tr>
			<td style="text-align: right; padding-right: 15px;">Nazwa odbiorcy:</td>
			<td>bethink sp. z o.o.</td>
		</tr>
		<tr>
			<td style="text-align: right; padding-right: 15px;">Adres odbiorcy:</td>
			<td>ul. Henryka Sienkiewicza 8/1, 60-817 Poznań</td>
		</tr>
		<tr>
			<td style="text-align: right; padding-right: 15px;">Kwota:</td>
			<td>{{ $order->total_with_coupon or '{12,000.00}' }}zł</td>
		</tr>
	</table>

	<p>Zarezerwowaliśmy dla Ciebie miejsce na kursie i przez najbliższe 7 dni czekamy na wpłatę. :) </p>

	<p>W razie pytań pisz śmiało na info@wiecejnizlek.pl!</p>

	<p>Do zobaczenia!</p>
@endsection
