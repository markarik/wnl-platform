@extends('mail.layout')

@section('content')
	<h3>Cześć {{ $user->first_name or '{first_name}' }}!</h3>

	<p style="font-size: 1.25em;">
		Elo elo, dostałeś koda!
		{{ $coupon->code }}
		<a href="{{ url( 'payment/voucher?code=' . $coupon->code ) }}" >
			{{ url( 'payment/voucher?code=' . $coupon->code ) }}
		</a>
	</p>

	<p>W razie pytań pozostajemy do dyspozycji!</p>

	<p>Z pozdrowieniami,</p>
@endsection
