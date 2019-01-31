@extends('mail.layout')

@section('content')
	<h3>Cześć {{ $order->user->first_name ?? '{first_name}' }}!</h3>

	<p>W załączniku przesyłamy fakturę korygującą do Twojego zamówienia. :)</p>

	<p>Wszystie faktury dotyczące Twoich zamówień znajdziesz w zakładce <a href="{{url('app/myself/orders')}}">KONTO > Twoje zamówienia</a>.</p>

	<p>W razie pytań napisz do nas na info@wiecejnizlek.pl</p>

	<p class="text-align: center;">
		<img src="https://media.giphy.com/media/yUrUb9fYz6x7a/giphy.gif" alt="Tyle papierologii..." style="display: block; margin: 0 auto;">
	</p>
@endsection
