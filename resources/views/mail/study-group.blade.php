@extends('mail.layout')

@section('content')
	<h3>Cześć!</h3>

	<p>
		Cieszymy się, że Ty i Twoja grupa dołączacie do nas! :)
	</p>

	<p>
		Twój kod Study Group o wartości {{ $coupon->value_with_unit }} możesz wykorzystać klikając tutaj: <br>
		<a href="{{ url( 'payment/voucher?code=' . $coupon->code ) }}" >
			{{ url( 'payment/voucher?code=' . $coupon->code ) }}
		</a>
	</p>

	<p>
		Kupon będzie ważny do {{ $coupon->expires_at->format('d.m.Y') }}.
	</p>

	<p>
		Pamiętaj proszę, aby zamówienie z użyciem kodu opłaciło co najmniej 10 osób, w przeciwnym wypadku anulujemy zniżkę.
	</p>

	<p>
		Gdyby nasunęły Ci się jakieś pytania lub wątpliwości, pisz śmiało! :)
	</p>

	<p>Z pozdrowieniami,</p>
@endsection
