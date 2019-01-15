@extends('mail.layout')

@section('content')
	<h3>Cześć {{ $user->first_name ?? '{first_name}' }}!</h3>
	<p style="font-size: 1.1rem;">Witaj w społeczności "Więcej niż LEK"! Niezmiernie cieszymy się, że do nas dołączasz!</p>
	<p>Ten mail jest potwierdzeniem założenia konta na naszej platformie e-learningowej, które potrzebujemy, aby sprawnie obsłużyć Twoje zamówienie.</p>

	<p>Jeśli wszystko poszło sprawnie, mail potwierdzający zamówienie zaraz pojawi się w Twojej skrzynce! :)</p>

	<p style="font-size: 1.1rem; font-weight: bold;">Przydatne linki</p>
	<ul>
		<li>
			<a href="https://platforma.wiecejnizlek.pl/login">
				Zaloguj się na platformie
			</a>
		</li>
		<li>
			<a href="https://platforma.wiecejnizlek.pl/app/myself/orders">
				Konto > Twoje zamówienia
			</a>
		</li>
		<li>
			<a href="https://platforma.wiecejnizlek.pl/payment/select-product">
				Wróć do procesu zakupowego
			</a>
		</li>
	</ul>

	<p>Z pozdrowieniami,</p>
@endsection
