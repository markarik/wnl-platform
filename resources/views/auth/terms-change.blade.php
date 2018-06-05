@extends('layouts.guest')

@section('content')
	<div class="container tou-container">
		<section class="subsection content">
			<h1>Cześć {{ $user->first_name }}!</h1>
			<p>
				W związku z wejściem w życie nowego Rozporządzenia o Ochronie Danych Osobowych (RODO), chcielibyśmy przypomnieć, że przechowujemy Twoje dane, które były potrzebne do obsługi Twojego zamówienia, wysyłki oraz obsługi konta.
			</p>
			<p>
				<strong>Co najważniejsze - nigdy nie planowaliśmy, nie planujemy i będziemy planować udostępnienia Twoich danych jakimkolwiek innym podmiotom. Wszystkie Twoje dane (oprócz e-maila, imienia i nazwiska) są też zapisywane w naszej bazie jako zaszyfrowane.</strong>
			</p>
			<p>
				Prosimy, zapoznaj się z naszą uaktualnioną <a href="">Polityką Prywatności</a>, w której znajdziesz informacje o prawach jakie posiadasz w związku ze swoimi danymi osobowymi oraz poznasz cele, w jakich przetwarzamy te informacje. Jeżeli akceptujesz treść dokumentu, potwierdź to kliknięciem w przycisk "AKCEPTUJĘ NOWĄ POLITYKĘ PRYWATNOŚCI". Jeśli masz wątpliwości dotycząte nowej Polityki Prywatności, napisz do nas na <a href="mailto:info@wiecejnizlek.pl">info@wiecejnizlek.pl</a>.
			</p>
			<p>
				Dziękujemy za wyrozumiałość! 🙂
			</p>
		</section>

		<section class="subsection">
			<form action="{{ route('terms-accept') }}" method="post">
				{{ csrf_field() }}
				<div class="has-text-centered">
					<button class="button is-primary">
						Akceptuję nową politykę prywatności
					</button>
				</div>
			</form>
		</section>

		<section class="subsection">

		</section>
	</div>
@endsection
