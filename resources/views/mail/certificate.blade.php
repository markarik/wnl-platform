@extends('mail.layout')

@section('content')
	<h3>Cześć {{ $user->first_name or '{first_name}' }}!</h3>

	<p>Dziękujemy Ci za udział w drugiej edycji kursu! W załączniku znajdziesz certyfikat jego ukończenia. :) Jeżeli planujesz uzyskać refundację kursu z Izby Lekarskiej, to może okazać się przydatny. ;) Jest on również dowodem przyznania punktów edukacyjnych.</p>

	<p>Na koniec mamy też do Ciebie skromną prośbę o podzielenie się swoją oceną kursu na <a href="https://www.facebook.com/pg/wiecejnizlek/reviews/" target="_blank">facebooku</a> oraz <a href="https://goo.gl/forms/sYLtHFS44WKL88Rp2">podzielenie się swoim wynikiem</a>! Rezultaty osób, które rzeczywiście ukończyły kurs są dla nas niezwykle istotne. Rozumiemy, że teraz pewnie Twoje myśli odbiegły już od nauki i kursu, ale może ten mail skłoni Cię do krótkiego powrotu do tego kontekstu. ;) </p>

	<p>Jeszcze raz gratulujemy Ci wytrwałości i życzymy powodzenia na dalszej drodze edukacyjnej!</p>

	<p>Do zobaczenia!</p>
@endsection
