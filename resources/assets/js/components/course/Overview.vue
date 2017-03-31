<template>
	<div>
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					{{ welcomeMessage }}
				</div>
			</div>
			<div class="level-right">

			</div>
		</div>
		<div class="wnl-overview-section">
			<wnl-next-lesson :courseId="courseId"></wnl-next-lesson>
		</div>
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

		<!-- Course overview placeholder -->

		<div class="level wnl-screen-title margin vertical">
			<div class="level-left">
				<div class="level-item big strong">
					Witaj! <wnl-emoji name="wave"></wnl-emoji>
				</div>
			</div>
		</div>
		<div class="content margin bottom">
			<p>Jest nam niezwykle miło gościć Cię na demostracyjnej wersji naszej platformy! To na niej spędzimy ze sobą najwięcej czasu i na niej kurs "Więcej niż LEK" będzie żył na co dzień.</p>
			<p>Demo będzie aktywne przez cały czas trwania zapisów - <strong>od 1 kwietnia, do 15 maja, lub do wyczerpania miejsc</strong>. W tym czasie możesz nie tylko przejść przykładowe lekcje, ale też dowiedzieć się stąd wszystkiego o założeniach projektu "Więcej niż LEK".</p>
			<p>Mamy też szczerą nadzieję, że wizyta tutaj zrodzi w Tobie pytania, na które będziemy mogli odpowiedzieć.</p>
			<h4>Co przygotowaliśmy?</h4>
			<p>Dla potrzeb demonstracji, przygotowaliśmy dla Ciebie 3 lekcje, mówiące przede wszystkim o samym kursie. W ostatniej jednak, znajdziesz próbkę prezentacji medycznej. Czekają na Ciebie kolejno lekcje:</p>
			<h5><router-link :to="{name: 'lessons', params: { courseId: 1, lessonId: 1 }}">1. O kursie</router-link></h5>
			<p>Co właściwie chcemy osiągnąć? Jak będzie przebiegał kurs?</p>
			<h5><router-link :to="{name: 'lessons', params: { courseId: 1, lessonId: 2 }}">2. O założeniach</router-link></h5>
			<p>Konstrukcja materiałów, plan pracy, systematyzacja wiedzy, wspólna nauka i wzajemna motywacja, czyli 5 filarów "Więcej niż LEK".</p>
			<h5><router-link :to="{name: 'lessons', params: { courseId: 1, lessonId: 3 }}">3. Przykładowa lekcja</router-link></h5>
			<p>Slajdy dające przedsmak kursu, prezentujące sposób formatowania treści na slajdach, oraz graficzną stronę prezentacji.</p>
			<h4>Czego nie ma na demo?</h4>
			<p>Do samego kursu (a właściwie to nieustannie) będziemy pracowali nad tym, aby nasza platforma stała się dla Was ulubionym środowiskiem nauki! Na razie skupiliśmy się na tym, aby zaprezentować Wam podstawowe funkcje - prezentacje, diagramy, filmy, czat.</p>
			<p>Tymczasem, za kulisami, szykujemy jeszcze system pytań i odpowiedzi, więcej opcji personalizacji, powiadomienia, prywatne wiadomości, pytania kontrolne i wiele, wiele innych fantastycznych pomysłów. Wszystkie będą pojawiać się na demo, jak tylko będą gotowe do pokazania światu.</p>
			<h4>Zaczynamy?</h4>
			<p>Na początek, chcielibyśmy podzielić się z Tobą tym, co dla nas najważniejsze - naszą&nbsp;misją!&nbsp;
				<wnl-emoji name="rocket"></wnl-emoji>
			</p>
			<p class="has-text-centered">
				<router-link class="button is-primary is-outlined" :to="{name: 'lessons', params: { courseId: 1, lessonId: 1 }}">
					Zacznij pierwszą lekcję - O kursie
				</router-link>
			</p>
		</div>
	</div>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'

	.content
		color: $color-gray

	.wnl-overview-section
		margin-bottom: $margin-big
</style>

<script>
	import emoji from 'node-emoji'
	import NextLesson from 'js/components/course/NextLesson.vue'
	import YourProgress from 'js/components/course/YourProgress.vue'
	import { mapGetters } from 'vuex'

	export default {
		props: ['courseId'],
		computed: {
			...mapGetters([
				'currentUserName',
			]),
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
