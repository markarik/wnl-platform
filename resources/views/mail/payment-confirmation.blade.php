@extends('mail.layout')

@section('content')
	<h3>Dziękujemy za wpłatę {{ $order->user->first_name or '{first_name}' }}!</h3>

	<p style="text-align: center;">
		<img src="https://media.giphy.com/media/11sBLVxNs7v6WA/giphy.gif" alt="Tak się cieszymy!" style="display: block; margin: 0 auto;">
	</p>

	{{-- <p>Już niedługo, 6 listopada, rozpoczniemy razem 14-tygodniowe przygotowania do LEK-u. Zrobimy wszystko, co w naszej mocy, aby ten czas był dla Ciebie nie tylko efektywny, ale też przyjemny! :)</p> --}}

	{{-- @if (is_object($order) && $order->studyBuddy && !in_array($order->studyBuddy->status, ['awaiting-refund', 'refunded']))
		<h3>Promocja Study Buddy</h3>
		<p>
			Przypominamy, że możesz skorzystać z promocji <strong>Study Buddy</strong>! Wyślij znajomej osobie swój unikalny kod: <strong>{{$order->studyBuddy->code or '{code}'}}</strong>, który będzie mogła wykorzystać przy rejestracji wchodząc na <a href="{{url('payment/voucher')}}" target="_blank">{{url('payment/voucher')}}</a>.
		</p>
		<p>
			Jeżeli zapisze się ona na kurs <strong>oraz opłaci zamówienie</strong>, obydwoje otrzymacie 100zł zniżkę! Przed dokonaniem zwrotu, odezwiemy się do Ciebie, aby uzyskać dane do przelewu. :)
		</p>
		<p>
			Dla ułatwienia, możesz też skopiować jej ten link:<br>
			<a href="{{url("payment/voucher?code={$order->studyBuddy->code}")}}" target="_blank">
				{{url("payment/voucher?code={$order->studyBuddy->code}")}}
			</a>. :)
		</p>
	@endif --}}

	{{-- <h3>Co teraz?</h3>
	<p>Wszystkie lekcje udostępniane będą na naszej <a href="{{url('/')}}" title="Platforma e-learningowa">platformie e-learningowej</a>. Na razie znajdziesz tam odliczanie do Kursu, dostęp do swojego Konta oraz Pomoc, ale wszystkie funkcje platformy możesz przetestować już na <a href="https://demo.wiecejnizlek.pl" title="Wersja demo platformy">wersji demonstracyjnej</a>. O wszystkich ważnych kwestiach będziemy informować na <a href="https://facebook.com/wiecejnizlek" title="Fanpage Więcej niż LEK">naszym fanpage'u</a>.</p> --}}

	<p>Wszystie faktury dotyczące Twoich zamówień znajdziesz w zakładce KONTO > Twoje zamówienia.</p>

	<p style="text-align: center;">
		<a class="button is-primary is-outlined" href="https://platforma.wiecejnizlek.pl">Przejdź na platformę</a>
	</p>

	<p>Gdyby pojawiły się u Ciebie jakieś wątpliwości, czy pytania - pisz śmiało na info@wiecejnizlek.pl lub odwiedź nas w biurze przy <a href="https://goo.gl/maps/WbgYF2vcb3A2" title="bethink na Google Maps">Sienkiewicza 8/1 w Poznaniu</a>.</p>

	<p>Do zobaczenia!</p>

	@if (isset($invoice) && $invoice)
		<p>P.S. W załączniku znajdziesz fakturę potwierdzającą wpłatę. :)</p>
	@endif
@endsection
