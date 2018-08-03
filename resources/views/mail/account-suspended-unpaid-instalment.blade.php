@extends('mail.layout')

@section('content')
	<h3>Cześć {{ $order->user->first_name or '{first_name}' }}!</h3>

	<p>Musieliśmy chwilowo wstrzymać Twój dostęp do platformy, ponieważ nie otrzymaliśmy {{ $instalment->order_number }}. raty za <strong>{{$order->product->name or '{product_name}'}}</strong>. 😔</p>

	<p>Jeśli coś Ci tu nie gra, prosimy napisz do nas na info@wiecejnizlek.pl!</p>

	<p>Twoje konto <strong>zostanie automatycznie odblokowane po zaksięgowaniu wpłaty.</strong> 🙂</p>

	<h4>Dane do przelewu</h4>
	<table style="font-size: 0.9em; line-height: 2em;">
		<tr>
			<td style="text-align: right; padding-right: 15px;">Tytuł przelewu:</td>
			<td><strong>Zamówienie numer {{ $order->id or '{order_no}' }} - {{ $instalment->order_number }}. rata</strong></td>
		</tr>
		<tr>
			<td style="text-align: right; padding-right: 15px;">Numer konta:</td>
			<td>82 1020 4027 0000 1102 1400 9197 (PKO BP)</td>
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
			<td>{{ $instalment->left_amount or '{price}' }}zł</td>
		</tr>
	</table>

	<p><strong>Status swojego zamówienia możesz śledzić na stronie <a href="{{url('app/myself/orders')}}">Konto > Twoje zamówienia</a>.</strong> Tam znajdziesz też wszystkie szczegóły dotyczące płatności oraz informacje o terminach i kwotach rat. 🙂</p>

	<p>Do zobaczenia!</p>
@endsection
