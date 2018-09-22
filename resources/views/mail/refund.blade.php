@extends('mail.layout')

@section('content')
	<p class="text-align: center;">
		<img src="https://media.giphy.com/media/prjtNfj7bcRz2/giphy.gif" alt="You'll get the money soon..." style="display: block; margin: 0 auto;">
	</p>

	<h3>Cześć {{ $user->first_name or '{first_name}' }}!</h3>

	<p style="font-size: 1.25em;">Wykonaliśmy zwrot {{ $value or '{value}' }}zł na Twoje konto!</p>

	<p>W załączniku znajdziesz fakturę korygującą do Twojego zamówienia. ;)</p>

	<p>Swoje zamówienia znajdziesz w zakładce <a href="{{url('app/myself/orders')}}" target="_blank">KONTO > Twoje zamówienia</a>. :)</p>

	<p>Z pozdrowieniami,</p>
@endsection
