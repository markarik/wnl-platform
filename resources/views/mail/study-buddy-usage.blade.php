@extends('mail.layout')

@section('content')
	<p class="text-align: center;">
		<img src="https://media.giphy.com/media/QbkL9WuorOlgI/giphy.gif" alt="Jak dobrze, że jesteś!" style="display: block; margin: 0 auto;">
	</p>

	<h3>Cześć {{ $user->first_name ?? '{first_name}' }}!</h3>

	<p style="font-size: 1.25em;">Skorzystaj ze zniżki Study Buddy - 100zł dla Ciebie i znajomej osoby!</p>

	<p>Wyślij znajomej osobie Twój unikalny kod:</p>

	<h3 class="has-text-centered" style="text-transform: uppercase;">{{ $coupon->code ?? '{code}' }}</h3>

	<p>Wpisując go na stronie <a href="{{url('payment/voucher')}}">{{url('payment/voucher')}}</a>, otrzyma 100zł zniżki na kurs!</p>

	<p>Gdy jej zamówienie zostanie <strong>poprawnie opłacone</strong> - Ty otrzymasz w przeciągu 7 dni zwrot na konto, z którego zostało opłacone Twoje zamówienie. W przypadku rat wystarczy płatność 1. raty. 🙂</p>

	<p>Zaproś swojego Study Buddy do wspólnej nauki! 🎓</p>

	<p>W razie pytań pisz na <a href="mailto:info@wiecejnizlek.pl">info@wiecejnizlek.pl</a>. 🙂</p>

	<p>Z pozdrowieniami,</p>
@endsection
