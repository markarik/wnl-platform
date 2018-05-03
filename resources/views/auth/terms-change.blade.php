@extends('layouts.guest')

@section('content')
	<div class="container tou-container">
		<section class="subsection content">
			<h1>Cześć {{ $user->first_name }}!</h1>
			<p>
				Ze względu na decyzje o umożliwieniu wcześniejszego dostępu
				do kursu, jego indywidualnego planowania, oraz zwiększonego
				zainteresowania, musieliśmy wprowadzić dodatkowe reguły
				dotyczące Gwarancji Satysfakcji. Gwarancja Satysfakcji to
				możliwość uzyskania <strong>100% zwrotu wpłaty</strong> za kurs,
				gdy <strong>uczestniczysz w nim rzetelnie</strong>, a mimo to
				nie zdasz LEK-u, do którego dana edycja kursu przygotowuje.
				Jest to nasza dobrowolna klauzula, chcemy abyście czuli się
				pewni co do jakości kursu. 🙂 Nie wynika ona natomiast w żaden
				sposób z praw konsumenta.
			</p>
			<p>
				Co to znaczy rzetelnie? W naszym rozumieniu wymaga
				to <strong>co najmniej</strong>:
				<ul>
					<li>300h aktywności na platformie w ramach danej edycji kursu,</li>
					<li>Co najmniej 80% ukończonych lekcji,</li>
					<li>Co najmniej 60% rozwiązanych pytań.</li>
				</ul>
				Te liczby wynikają z gruntownej analizy przeprowadzanych
				ankiet i obserwacji efektów edukacyjnych kursu. Został on
				zaprojektowany w oparciu o wiele najnowszych wytycznych i każdy
				jego element ma znaczenie - ilość lekcji, ich kolejność,
				częstotliwość powtórek, pytań zamkniętych itd.
			</p>
			<p>
				Te reguły <strong>wciąż obowiązują</strong>. Jeżeli nie planujesz
				zmieniać planu kursu, czy zamieniać kolejności lekcji,
				to nic się dla Ciebie nie zmienia. Jeżeli jednak planujesz
				stosować jakieś modyfikacje - zacząć wcześniej naukę, uczyć się
				z platformy do egzaminów, czy przyspieszyć naukę - i jednocześnie
				chcesz zachować Gwarancję Satysfakcji - prosimy,
				zwróć uwagę na nowe zasady:
				<ul>
					<li>
						Plan, który realizujesz, nie może przewidywać więcej,
						niż 1 lekcji na 1 dzień.
					</li>
					<li>
						Plan, który realizujesz, musi zakładać
						ukończenie <strong>wszystkich lekcji
						w co najwyżej 6 miesięcy.</strong>
					</li>
					<li>
						Możesz zrealizować co najwyżej 10 lekcji poza kolejnością,
						czyli np. zacząć od Pediatrii, zamienić Ginekologię
						i Psychiatrię. Nie możesz natomiast zakończyć na Internie. 😉
					</li>
					<li>
						Jeżeli zaczynasz naukę przed oficjalnym startem kursu,
						to możesz dokonać zmian, które łamią powyższe zasady,
						ale zachować Gwarancję Satysfakcji, jeżeli z dniem
						rozpoczęcia kursu <strong>wrócisz do domyślnego
						planu</strong>. Jest to wyjście naprzeciw osobom,
						które chcą uczyć się z platformy do egzaminów,
						a potem w normalnym trybie zrealizować kurs. 🙂
					</li>
				</ul>
			</p>
			<p>
				Aby doprecyzować, chcielibyśmy podać kilka przykładów zachowania
				Gwarancji Satysfakcji. <strong>Możesz</strong> z niej skorzystać
				między innymi, gdy spełnisz warunki 300h, 80% lekcji i 60% bazy oraz:
				<ul>
					<li>Nie zmieniasz planu kursu</li>
					<li>
						Zaczniesz naukę 15 maja i zmienisz plan kursu na
						trwający 4 miesiące, bez zmiany kolejności lekcji.
					</li>
					<li>
						Zaplanujesz naukę do <strong>lutowego LEK-u</strong>
						na czas nie dłuższy, niż 6 miesięcy.
					</li>
					<li>
						Przed kursem zrealizujesz jeden, lub dwa przedmioty, potrzebne
						Ci do egzaminu, nie przekraczające łącznie 10 lekcji.
					</li>
					<li>
						Z dniem 9 czerwca powrócisz do domyślnego planu lekcji.
					</li>
				</ul>
			</p>
			<p>
				Dodatkowo, musimy niestety wprowadzić jeszcze jedno ograniczenie.
				Gwarancja Satysfakcji dotyczy <strong>tylko osób kończących
				studia w języku polskim, na polskiej uczelni</strong>.
				Gdy oprócz złożoności merytorycznej nauki, pojawia się
				jeszcze bariera językowa, nie możemy zagwarantować, że
				praca z kursem wystarczy, aby uzyskać pozytywny wynik.
			</p>
			<p>
				Akceptacja Regulaminu jest konieczna, aby korzystać z kursu.
				Jeśli masz wątpliwości dotycząte nowego Regulaminu lub
				chcesz zrezygnować z kursu (bez konsekwencji), napisz do nas na
				<a href="mailto:info@wiecejnizlek.pl">info@wiecejnizlek.pl</a>
			</p>
			<p>
				Nowy regulamin znajdziesz
				<a href="@lang('payment.tou-link-href')" target="_new"> tutaj</a>,
				a zapisy o Gwarancji Satysfakcji w jego VII punkcie.
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
						Akceptuję nowy regulamin
					</button>
				</div>
			</form>
		</section>

		<section class="subsection">

		</section>
	</div>
@endsection
