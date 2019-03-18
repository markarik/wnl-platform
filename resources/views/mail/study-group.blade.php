@extends('mail.layout')

@section('content')

	<h3>Cześć!</h3>

	<p>
		Cieszymy się, że Ty i Twoja grupa dołączacie do nas! :)
	</p>

	<p class="has-text-centered" style="text-transform: uppercase;">Twój kod Study Group o wartości {{ $coupon->value_with_unit ?? '{value_with_unit}' }}, to:</p>

	<h3 class="has-text-centered" style="text-transform: uppercase;">{{ $coupon->code ?? '{code}' }}</h3>

	<p class="has-text-centered" style="margin: 20px 0;">
		<a href="https://wiecejnizlek.pl/voucher" class="button">
			Kliknij i wykorzystaj zniżkę
		</a>
	</p>

	<p>
		Kupon będzie ważny do <strong>{!! isset($coupon) && isset($coupon->expires_at) ? $coupon->expires_at->format('d.m.Y') : '{expires}' !!}</strong>.
	</p>

	<h4>
		Przypominamy, że nie ma już możliwości dopisywania osób do Waszej listy. Dziękujemy za wyrozumiałość!
	</h4>

	<p>
		Jako grupa pamiętajcie, aby zamówienia z użyciem swoich kodów opłaciło co najmniej 10 osób - w przeciwnym wypadku będziemy musieli anulować zniżkę. :(
	</p>

	<p>
		Gdyby nasunęły Ci się jakieś pytania lub wątpliwości, pisz na info@wiecejnizlek.pl! :)
	</p>

	<p class="text-align: center;">
		<img src="https://media.giphy.com/media/wSNDFkJywdW24/giphy.gif" alt="Dobrze, że jesteście!" style="display: block; margin: 0 auto;">
	</p>

	<p>Z pozdrowieniami,</p>
@endsection
