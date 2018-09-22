@extends('mail.layout')

@section('content')
	<h3>Witaj ponownie {{ $order->user->first_name or '{first_name}' }}!</h3>

	<p>Piszemy do Ciebie, żeby przypomnieć o płatności za zamówienie nr {{ $order->id or '{order_no}' }} na kurs <strong>{{$order->product->name or '{product_name}'}}</strong>.</p>

	<p>W przypadku braku płatności, zamówienie zostanie <strong>automatycznie anulowane po upływie 2 dni roboczych.</strong> 😔</p>

	<p>Zamówienie możesz opłacić w zakładce <a href="{{url('app/myself/orders')}}" target="_blank">KONTO > Twoje zamówienia</a>.</p>

	<p class="has-text-centered" style="margin: 20px 0;">
		<a href="{{url('app/myself/orders')}}" class="button">
			Opłać zamówienie
		</a>
	</p>

	<p>W razie pytań pisz śmiało na info@wiecejnizlek.pl!</p>

	<p>Do zobaczenia!</p>
@endsection
