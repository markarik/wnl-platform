@extends('mail.newsletter.layout')

@section('content')

	<h3>Cześć %recipient_name%!</h3>

	<p><strong>Przyszedł moment weryfikacji naszych wspólnych starań!</strong></p>

	<p>Tak jak wspominaliśmy w trakcie kursu, bardzo prosimy Cię o podzielenie się z nami swoim wynikiem w anonimowej ankiecie! Jest ona dla nas niezwykle ważna, byśmy mogli ocenić nasze dotychczasowe działania oraz lepiej zaplanować kolejne kroki. :)</p>

	<div style="text-align: center; margin: 10px auto 20px;">
		<a class="button" href="https://goo.gl/forms/z15xkbbSoaW93Vjr2">
			Przejdź do ankiety
		</a>
	</div>

	<hr style="border: 0; height: 1px; background-image: linear-gradient(to right, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0));">

	<p>Jeżeli możemy przy okazji mieć małą prośbę - jeśli masz ochotę i czas podzielić się swoimi wrażeniami z kursu na facebooku bylibyśmy niezwykle wdzięczni! Sporo osób czeka na relacje z pierwszej ręki! ;)</p>

	<div style="text-align: center; margin: 10px auto 20px;">
		<a class="button" href="https://www.facebook.com/wiecejnizlek/reviews">
			Zostaw recenzję
		</a>
	</div>

	<hr style="border: 0; height: 1px; background-image: linear-gradient(to right, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0));">

	<p>Życzymy Ci powodzenia na ścieżce naukowo-zawodowej! Mamy nadzieję, że przyjdzie nam jeszcze wspólnie uczyć się i rozwijać przy okazji kolejnych projektów "Więcej niż LEK"! Do zobaczenia!</p>
@endsection
