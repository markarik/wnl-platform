@extends('mail.layout')

@section('content')
	<h3>Cześć {{ $order->user->first_name or '{first_name}' }}!</h3>

	<p>W załączniku znajdziesz fakturę końcową bla bla bla bla</p>
@endsection
