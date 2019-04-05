@extends('mail.layout')

@section('content')
	<h3>Cześć {{ $user->first_name ?? '{first_name}' }}!</h3>

	<p>Dziękujemy za dołączenie do kursu "Więcej niż LEK", który właśnie się rozpoczął! W załączniku znajdziesz symboliczny certyfikat uczestnictwa. 🙂</p>

	<p>Życzymy Ci powodzenia i wytrwałości w nauce! Pamiętaj, że w razie dużych zaległości, problemów z tempem kursu lub innych przeszkód w jego realizacji możesz do nas śmiało pisać <a href="{{ route('app/help') }}">na platformie</a> lub <a href="https://fb.com/wiecejnizlek">na facebooku</a>. 🙂</p>

	<p>Pozdrawiamy serdecznie!</p>
@endsection
