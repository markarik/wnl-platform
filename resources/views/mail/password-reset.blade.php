@extends('mail.layout')

@section('content')
	<p>Otrzymałeś tego maila, ponieważ bla bla bla</p>
	<a class="button" href="{{ route('password.reset', $token) }}">Resetuj hasło</a>\
	<p>Jeśli to nie Ty, to bla bla bla</p>
@endsection