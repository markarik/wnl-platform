@extends('mail.layout')

@section('content')
	<h3>Witaj ponownie {{ $order->user->first_name ?? '{first_name}' }}!</h3>

	<p>Piszemy do Ciebie, żeby przypomnieć o płatności za zamówienie nr {{ $order->id ?? '{order_no}' }} na kurs <strong>{{$order->product->name ?? '{product_name}'}}</strong>.</p>

	<p>W przypadku braku płatności, zamówienie zostanie <strong>automatycznie anulowane po upływie 2 dni roboczych.</strong> 😔</p>

	<p><strong>Opłacić zamówienie możesz w zakładce <a href="{{url('app/myself/orders')}}">Konto > Twoje zamówienia</a>.</strong> Tam znajdziesz też wszystkie szczegóły dotyczące płatności. 😔</p>

	<p class="has-text-centered" style="margin: 20px 0;">
		<a href="{{url('app/myself/orders')}}" class="button">
			OPŁAĆ ZAMÓWIENIE
		</a>
	</p>

	<p>W razie pytań pisz śmiało na info@wiecejnizlek.pl! 😔</p>

	<p>Do zobaczenia!</p>
@endsection
