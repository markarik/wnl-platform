@extends('mail.layout')

@section('content')
	<h3>Cześć {{ $user->first_name }}!</h3>
	<h4>Ta wiadomość została wysłana automatycznie w odpowiedzi na prośbę o zmianę hasła, przypisanego do tego adresu e-mail.</h4>
	<p><strong>Jeżeli to nie Ty stoisz za tą prośbą - zignoruj tę wiadomość.</strong></p>
	<p>Jeżeli jednak faktycznie chcesz zmienić hasło, kliknij na przycisk <strong>Resetuj hasło</strong>, aby przejść dalej.</p>
	<p class="has-text-centered">
		<a class="button is-primary" href="{{ route('password.reset', $token) }}">Resetuj hasło</a>
	</p>
	<p>Pozdrawiamy!</p>
@endsection
