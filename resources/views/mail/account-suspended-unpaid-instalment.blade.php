@extends('mail.layout')

@section('content')
	<h3>Cześć {{ $order->user->first_name ?? '{first_name}' }}!</h3>

	<p>Musieliśmy chwilowo wstrzymać Twój dostęp do platformy, ponieważ nie otrzymaliśmy {{ $instalment->order_number ?? '{order_number}' }}. raty za <strong>{{$order->product->name ?? '{product_name}'}}</strong>. 😔</p>

	<p>Ratę możesz opłacić w zakładce <a href="{{url('app/myself/orders')}}" target="_blank">KONTO > Twoje zamówienia</a>.</p>

	<p class="has-text-centered" style="margin: 20px 0;">
		<a href="{{url('app/myself/orders')}}" class="button">
			Zapłać kolejną ratę
		</a>
	</p>

	<p>Jeśli coś Ci tu nie gra, prosimy napisz do nas na info@wiecejnizlek.pl!</p>

	<p>Twoje konto <strong>zostanie automatycznie odblokowane po zaksięgowaniu wpłaty.</strong> 🙂</p>

	<p>Do zobaczenia!</p>
@endsection
