@extends('mail.layout')

@section('content')
	<h3>Cześć {{ $order->user->first_name or '{first_name}' }}!</h3>

	<p>Niedawno wysłaliśmy Ci fakturę, na której, jak później zauważyliśmy, nie wszystkie informacje były poprawnie zwizualizowane.
		W związku z tym przesyłamy teraz w załączniku poprawiony dokument.</p>

	<p>Przepraszamy za zamieszanie i życzymy miłego dnia :)</p>

	<p>Szczegóły zamówienia znajdziesz w zakładce <a href="{{url('app/myself/orders')}}" target="_blank">KONTO > Twoje zamówienia</a>. :)</p>

	<p class="text-align: center;">
		<img src="https://media.giphy.com/media/yUrUb9fYz6x7a/giphy.gif" alt="Tyle papierologii..." style="display: block; margin: 0 auto;">
	</p>

	<p>Z pozdrowieniami,</p>
@endsection
