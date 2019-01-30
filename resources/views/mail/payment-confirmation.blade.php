@extends('mail.layout')

@section('content')
	<h3>Dziękujemy za wpłatę {{ $order->user->first_name ?? '{first_name}' }}!</h3>

	<p style="text-align: center;">
		<img src="https://media.giphy.com/media/11sBLVxNs7v6WA/giphy.gif" alt="Tak się cieszymy!" style="display: block; margin: 0 auto;">
	</p>

	<p>Wszystie faktury dotyczące Twoich zamówień znajdziesz w zakładce <a href="{{url('app/myself/orders')}}" target="_blank">KONTO > Twoje zamówienia</a>. :)</p>

	<p style="text-align: center;">
		<a class="button is-primary is-outlined" href="{{url('app/myself/orders')}}">Przejdź do Twoich zamówień</a>
	</p>

	<p>Gdyby pojawiły się u Ciebie jakieś wątpliwości, czy pytania - pisz śmiało na info@wiecejnizlek.pl!</p>

	<p>Do zobaczenia!</p>

	@if (isset($invoice) && $invoice)
		<p>P.S. W załączniku znajdziesz fakturę potwierdzającą wpłatę. :)</p>
	@endif
@endsection
