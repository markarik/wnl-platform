@extends('mail.layout')

@section('content')
	<h3>Witaj ponownie {{ $order->user->first_name or '{first_name}' }}!</h3>

	<p>Piszemy do Ciebie, żeby przypomnieć o płatności za zamówienie nr {{ $order->id or '{order_no}' }} na kurs <strong>{{$order->product->name or '{product_name}'}}</strong>.</p>

	<p>W przypadku braku płatności, zamówienie zostanie <strong>automatycznie anulowane po upływie 2 dni roboczych.</strong> 😔</p>

	<p><strong>Status swojego zamówienia możesz śledzić na stronie <a href="{{url('app/myself/orders')}}">Konto > Twoje zamówienia</a>.</strong> Tam znajdziesz też wszystkie szczegóły dotyczące płatności. 🙂</p>

	<p>Płatności przelewem bankowym możesz dokonać przy użyciu poniższych danych:</p>

	<h4>Dane do przelewu</h4>
	<table style="font-size: 0.9em; line-height: 2em;">
		<tr>
			<td style="text-align: right; padding-right: 15px;">Tytuł przelewu:</td>
			<td><strong>Zamówienie numer {{ $order->id or '{order_no}' }}</strong></td>
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
			<td>{{ $order->total_with_coupon or '{price}' }}zł</td>
		</tr>
	</table>

	<p>W razie pytań pisz śmiało na info@wiecejnizlek.pl!</p>

	<p>Do zobaczenia!</p>
@endsection
