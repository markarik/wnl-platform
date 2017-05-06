<template>
	<div class="scrollable-main-container">
		<!-- Next lesson -->
		<div v-if="!isBeginning">
			<div class="wnl-overview-section">
				<wnl-next-lesson :courseId="courseId"></wnl-next-lesson>
			</div>
		</div>

		<!-- Your progress -->
		<div v-if="!isBeginning && !isEnd">
			<div class="level wnl-screen-title">
				<div class="level-left">
					<div class="level-item big strong">
						Jak Ci idzie?
					</div>
				</div>
			</div>
			<div class="wnl-overview-section">
				<wnl-your-progress :courseId="courseId"></wnl-your-progress>
			</div>
		</div>

		<!-- Course overview placeholder -->
		<div v-if="isBeginning">
			<h4 class="margin bottom">Witaj {{currentUserName}}! <wnl-emoji name="wave"></wnl-emoji></h4>
			<div class="content margin bottom">
				<p>Jest nam niezwykle miło gościć Cię na demostracyjnej wersji naszej platformy! To na niej spędzimy ze sobą najwięcej czasu i na niej kurs "Więcej niż LEK" będzie żył na co dzień.</p>
				<p>Demo będzie aktywne nie tylko przez cały czas trwania zapisów - <strong>od 1 kwietnia, do 15 maja, lub do wyczerpania miejsc</strong>, ale też do rozpoczęcia kursu. W tym czasie możesz przejść przykładowe lekcje, oraz dowiedzieć się stąd wszystkiego o założeniach projektu "Więcej niż LEK". Tu na bieżąco będziemy też publikować nowe funkcje, które nieustająco dodajemy.</p>
				<p>Mamy też szczerą nadzieję, że wizyta tutaj zrodzi w Tobie pytania, na które będziemy mogli odpowiedzieć.</p>
				<h4>Co przygotowaliśmy?</h4>
				<p>Dla potrzeb demonstracji, przygotowaliśmy dla Ciebie 3 lekcje, mówiące przede wszystkim o samym kursie. W ostatniej jednak, znajdziesz próbkę prezentacji medycznej. Czekają na Ciebie kolejno lekcje:</p>
				<h5><router-link :to="{name: 'lessons', params: { courseId: 1, lessonId: 1 }}">1. O kursie</router-link></h5>
				<p>Co właściwie chcemy osiągnąć? Jak będzie przebiegał kurs?</p>
				<h5><router-link :to="{name: 'lessons', params: { courseId: 1, lessonId: 2 }}">2. O założeniach</router-link></h5>
				<p>Konstrukcja materiałów, plan pracy, systematyzacja wiedzy, wspólna nauka i wzajemna motywacja, czyli 5 filarów "Więcej niż LEK".</p>
				<h5><router-link :to="{name: 'lessons', params: { courseId: 1, lessonId: 3 }}">3. Przykładowa lekcja</router-link></h5>
				<p>Slajdy dające przedsmak kursu, prezentujące sposób formatowania treści na slajdach, oraz graficzną stronę prezentacji.</p>
				<h4>Co znajdziecie na demo?</h4>
				<p>Do samego kursu (a właściwie to nieustannie) będziemy pracowali nad tym, aby nasza platforma stała się dla Was ulubionym środowiskiem nauki! Oprócz podstawowych funkcji - prezentacji, diagramów, filmów, czatu - staramy się pokazać jak najwięcej wyróżniających naszą platformę aspektów. Stąd prezentacja systemu pytań i odpowiedzi, czy możliwość rozwiązania przykładowego zestawu pytań po <router-link :to="{name: 'lessons', params: { courseId: 1, lessonId: 3 }}">przykładowej lekcji z Reumatologii</router-link>.</p>
				<p>Tymczasem, za kulisami, szykujemy jeszcze wiele rzeczy, które uprzyjemnią Wam korzystanie z platformy - między innymi zapisywanie ważnych pytań i slajdów do własnych Kolekcji, notyfikacje o aktywności innych użytkowników oraz obszerną i szyką wyszukiwarkę wiedzy.</p>
				<h4>Zaczynamy?</h4>
				<p class="has-text-centered">
					<router-link class="button is-primary is-outlined" :to="{name: 'lessons', params: { courseId: 1, lessonId: 1 }}">
						Zacznij pierwszą lekcję - O kursie
					</router-link>
				</p>
			</div>
		</div>
		<div v-if="isEnd">
			<div class="level wnl-screen-title margin vertical">
				<div class="level-left">
					<div class="level-item big strong">
						Misja "Więcej niż LEK"
					</div>
				</div>
			</div>
			<div class="content margin bottom">
				<blockquote>"Niezadowoleni są siłą napędową postępu"</blockquote>

				<p>Na koniec, chcielibyśmy podzielić się z Tobą naszą perspektywą na cel, który sobie założyliśmy. Mamy nadzieję, że pozwoli Ci ona spojrzeć z innej strony na to co wspólnie próbujemy stworzyć.</p>

				<p>Pewnie domyślasz się, że nie mamy tutaj na myśli tylko przygotowania do LEK-u. Problem, który próbujemy wspólnie rozwiązać, jest zdecydowanie bardziej złożony. LEK i jego konstrukcja, są zaledwie jego objawem. Przyczyny leżą głębiej i dlatego ten kurs, w swoich założeniach, jest czymś więcej, niż tylko kursem do LEK-u. Tak jak wiele inicjatyw w dzisiejszym świecie, tak i projekt “Więcej niż LEK” zrodził się z frustracji i niezadowolenia.</p>

				<p>Aktualny obraz polskiej edukacji, zarówno w okresie przed studiami, w trakcie, jak i po nich, nie maluje się w jaskrawych barwach. Wszyscy należymy do pokolenia, któremu bardzo wiele obiecywano. Pewne wykształcenie, praca, specjalizacja, czy po prostu łatwa i spokojna przyszłość. Każdy z nas w swym dorosłym życiu, wcześniej lub później zrozumiał, że rzeczywistość wygląda zgoła inaczej. Wiedza, dyplom, czy tytuł, niczego już nie gwarantują, a wymagania względem młodych ludzi zmieniają się średnio co dekadę.</p>

				<p>Tak też było w naszym przypadku. Oczywiście - "nie jest aż tak źle". Niespełnione obietnice można przemilczeń, nierealistyczne obrazy zrzucić na karb naiwności. Po prostu weźmiemy się do pracy i na wszystko sami z czasem zapracujemy.</p>

				<p>Istnieje jednak aspekt, którego przemilczeń nie mamy zamiaru. Śmiało i otwarcie mówimy, jako zespół i jako grupa przyjaciół, że post-industrialny system edukacji powoli i systematycznie, odbierał nam coś, co jest najcenniejszym skarbem każdego człowieka. Zdolność do samodzielnego, krytycznego i kreatywnego myślenia.</p>

				<p>Jakie są tego konsekwencje dla nas, lekarzy?</p>

				<p>Przede wszystkim, jako absolwenci studiów medycznych, nie znamy swojej wartości. Często nie potrafimy wyobrazić sobie naszej przyszłości. Mamy problem z podejmowaniem decyzji. Nie potrafimy negocjować i walczyć o swoje, kiedy już zaczynamy pracować. Nie umiemy pracować zespołowo, gdyż całe życie byliśmy oceniani indywidualnie. Ponadto, zakorzeniono w nas niezdrowy nawyk do uczestnictwa w "wyścigu szczurów", oraz dzielące środowisko medyczne podejście, w którym akademicka ścieżka rozwoju zawodowego jest z jakiegoś powodu "najlepsza".</p>

				<p>Kolejna konsekwencja tego zjawiska może ujawnić się parę lat później. My sami, jako specjaliści, przestaniemy uczyć innych, zapomnimy jak to było, kiedy sami potrzebowaliśmy pomocy i staniemy się częścią odwiecznego cyklu obojętności i nieświadomości.</p>

				<p class="big strong">Jeśli pragniemy, aby dzielenie się wiedzą, wzajemna pomoc i mentoring powróciły do polskich szpitali – sami powinniśmy stanowić tego taki przykład. To my powinniśmy być tą zmianą.</p>

				<p>Wszystkie te kwestie powodują, że jako grupa, nie wykorzystujemy w pełni swojego potencjału. Nie przekazujemy wiedzy młodszym adeptom sztuki lekarskiej, a jako specjaliści odcinamy się od środowiska zawodowego, myśląc często tylko o indywidualnej praktyce.</p>

				<p>Każdy z naszych projektów, niezależnie od tego, czy będzie związany z edukacją medyczną, czy edukacją w ogóle, będzie miał na celu przede wszystkim szerzenie idei wspólnego i zorganizowanego wysiłku. Wspólnej nauki, dzielenia się wiedzą i tworzenia cennych relacji między ludźmi. Pomaganie innym w staniu się samodzielnymi, kreatywnymi i pewnymi siebie ludźmi. Wierzymy, że w medycynie i pracy lekarza, jest to podstawa dobrze funkcjonującego zespołu i zdrowych relacji między lekarzami a pacjentami.</p>

				<p>Życie szybko uświadamia nam, że każdy z nas jest w pełni odpowiedzialny za swój rozwój. Zarówno w samej pracy, jak i poza nią, niczego nie przyniesie nam obarczanie winą za kiepską edukację systemu, współpracowników, czy atmosfery na oddziale. Może wręcz spowodować, że całkowicie zamkniemy się na możliwości, które posiadamy. Musimy więc wziąć sprawy w swoje ręce.</p>

				<p>Kto wie, być może dzięki naszemu wspólnemu wysiłkowi, za jakiś czas istnienie w Polsce efektywnego, systemowego nauczania sztuki lekarskiej, relacji uczeń - mistrz czy wielodyscyplinarnych, efektywnie działających zespołów medycznych będzie tak samo oczywiste, jak obecnie ich brak.</p>

				<p>Jeśli każdy z nas, na każdym kroku w pracy czy poza nią, będzie o tym pamiętał. Jeśli za każdym razem zachowamy się w odpowiedni sposób to inni także zaczną to robić. Z czasem, będzie to naszą wspólną codziennością.</p>

				<p>Zapraszamy Ciebie do bycia częścią tej zmiany.</p>

				<p>Uczmy się razem,<br>
				Pracujmy razem,<br>
				Leczmy razem,<br>
				i razem budujmy nowy obraz zawodu lekarza w polskim społeczeństwie.</p>

				<p><em>Zespół "Więcej niż LEK"</em></p>
			</div>
		</div>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.content
		color: $color-gray

	.wnl-overview-section
		margin-bottom: $margin-big

	.wnl-overview
		padding-bottom: 20em
</style>

<script>
	import emoji from 'node-emoji'
	import NextLesson from 'js/components/course/NextLesson.vue'
	import YourProgress from 'js/components/course/YourProgress.vue'
	import { mapGetters } from 'vuex'

	export default {
		props: ['courseId'],
		computed: {
			...mapGetters('progress', [
				'isLessonComplete',
				'wasCourseStarted',
			]),
			...mapGetters([
				'currentUserName',
			]),
			isBeginning() {
				return !this.wasCourseStarted(this.courseId)
			},
			isEnd() {
				// TODO: Apr 1, 2017 - Use API to get this infromation
				return this.isLessonComplete(this.courseId, 3)
			},
			welcomeMessage() {
				return `Cześć ${this.currentUserName}!`
			},
		},
		components: {
			'wnl-next-lesson': NextLesson,
			'wnl-your-progress': YourProgress,
		},
	}
</script>
