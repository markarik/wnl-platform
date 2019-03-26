@extends('mail.layout')

@section('content')
	<h3>Cześć!</h3>
	<p style="font-size: 1.1rem;">Witaj w społeczności "Więcej niż LEK"! Niezmiernie cieszymy się, że do nas dołączysz!</p>
	<p>Ten mail jest potwierdzeniem założenia konta na naszej platformie e-learningowej. Ta wiadomość pomoże nam sprawnie obsłużyć Twoje zamówienie. :)</p>

	<p>Jeśli wszystko poszło po naszej myśli, mail potwierdzający zamówienie zaraz pojawi się w Twojej skrzynce! :)</p>

	<a href="{{url('payment/account')}}">
		Wróć do procesu zakupowego
	</a>
	<p class="text-align: center;">
		<img src="https://media.giphy.com/media/KmEzemwIqhuF2/giphy.gif" alt="Widzimy się na platformie!" style="display: block; margin: 0 auto;">
	</p>

	<p>Z pozdrowieniami,</p>
@endsection
