@extends('mail.layout')

@section('content')
	<h3>Cześć!</h3>

	<p style="font-size: 1.25em;">
		Przesyłamy Ci kupon zniżkowy na kurs Więcej Niż LEK o wartości {{ $coupon->value_with_unit }},
		ważny do {{ $coupon->expires_at }}
	</p>

	<p>
		Możesz go wykorzystać przechodząc pod adres:
		<br>
		<a href="{{ url( 'payment/voucher?code=' . $coupon->code ) }}" >
			{{ url( 'payment/voucher?code=' . $coupon->code ) }}
		</a>
	</p>

	<p>W razie pytań pozostajemy do dyspozycji!</p>

	<p>Z pozdrowieniami,</p>
@endsection
