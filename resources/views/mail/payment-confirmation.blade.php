@extends('mail.layout')

@section('content')
	<h3>Dziękujemy za wpłatę {{ $order->user->first_name or '{Adam}' }}!</h3>

	<p class="text-align: center;">
		<img src="https://media.giphy.com/media/11sBLVxNs7v6WA/giphy.gif" alt="Tak się cieszymy!" style="display: block; margin: 0 auto;">
	</p>

	<p>Już niedługo, 17 czerwca, rozpoczniemy razem 12-tygodniowe przygotowania do LEK-u. Zrobimy wszystko, co w naszej mocy, aby ten czas był dla Ciebie nie tylko efektywny, ale też przyjemny! :)</p>

	<p>Wszystkie lekcje udostępniane będą na naszej <a href="https://platforma.wiecejnizlek.pl" title="Platforma e-learningowa">platformie e-learningowej</a>. Na razie znajdziesz tam tylko odliczanie do kursu, ale na <a href="https://demo.wiecejnizlek.pl" title="Wersja demo platformy">wersji demonstracyjnej</a> będą na bieżąco pojawiać się nowe elementy naszej aplikacji. O wszystkich będziemy informować na <a href="https://facebook.com/wiecejnizlek" title="Fanpage Więcej niż LEK">naszym fanpage'u</a>.</p>

	<p>Gdyby pojawiły się u Ciebie jakieś wątpliwości, czy pytania - pisz śmiało na info@wiecejnizlek.pl lub odwiedź nas w biurze przy <a href="https://goo.gl/maps/WbgYF2vcb3A2" title="bethink na Google Maps">Sienkiewicza 8/1 w Poznaniu</a>.</p>

	<p>Do zobaczenia wkrótce!</p>

	<p>P.S. W załączniku znajdziesz fakturę potwierdzającą wpłatę. :)</p>
@endsection
