<template>
	<div class="notification content" v-if="showNews">
		<button class="delete" @click="seenCurrentNews"></button>

		<p class="has-text-centered"><strong>SPISYWANIE PYTAŃ</strong></p>

		<p class="strong">Kochane Doktory!</p>

		<p>Chcemy Was dziś zaprosić do uczestnictwa w pierwszej, organizowanej przez nas akcji spisywania pytań z LEK-u! Wszystkie pytania, które jako młodzi lekarze mamy dziś dostępne, zostały spisane przez poprzednie roczniki - dołóżmy teraz naszą cegiełkę! 🙂</p>

		<p>Celem jest wiarygodne odtworzenie treści pytań. Jak możecie się domyślać jest to możliwe tylko dzięki Waszej pomocy. 😉</p>

		<p>Aby wziąć udział w akcji wystarczy przejść do ankiety dotyczącej zbierania pytań. 👇</p>

		<p class="aligncenter">
			<a href="https://goo.gl/forms/aLv3eRJRNKpya8ey1" target="_blank" class="button is-primary">
				Dołącz do spisywania pytań
			</a>
		</p>

		<p class="strong">Dziś prosimy Was tylko o wylosowanie w ankiecie numeru pytania oraz informację, czy Wasz numer kodowy z CEM jest parzysty, czy nieparzysty.</p>

		<p>Po egzaminie znajdziecie na platformie przypięty link do głównego pliku dotyczącego spisywania pytań.</p>

		<p class="strong">Pamiętajcie, że pytania najlepiej spisywać na gorąco, dlatego wróćcie na platformę jak najszybciej po wyjściu z egzaminu!</p>

		<p>Od października zajmiemy się opracowaniem i kategoryzacją pytań. W bazie pytań pojawią się już na start 4. edycji kursu! 🚀</p>

		<p>Dziękujemy za zaangażowanie!</p>

		<p style="font-style: italic;">Ekipa Więcej niż LEK</p>
	</div>
</template>

<script>
	import store from 'js/services/messagesStore'
	import { mapGetters } from 'vuex'

	const CURRENT_NEWS = 'edition-3-questions-collect'
	const DISPLAY_FROM = '' // new Date() or empty string
	const DISPLAY_UNTIL = new Date(2018, 8, 22, 8) // new Date() or empty string
	const REQUIRED_ROLE = ''

	export default {
		name: 'DashboardNews',
		data() {
			return {
				showNews: false
			}
		},
		computed: {
			...mapGetters(['currentUserName', 'hasRole']),
			hasNews() {
				return CURRENT_NEWS !== ''
			},
			hasRequiredRole() {
				return REQUIRED_ROLE === '' || this.hasRole(REQUIRED_ROLE)
			},
			hasSeenNews() {
				return !!store.get(this.newsStoreKey)
			},
			isNewsTimely() {
				const now = new Date()
				return (!(DISPLAY_FROM instanceof Date) || DISPLAY_FROM < now) &&
				(!(DISPLAY_UNTIL instanceof Date) || DISPLAY_UNTIL > now)
			},
			newsStoreKey() {
				return `seen-dashboard-news-${CURRENT_NEWS}`
			},
		},
		methods: {
			seenCurrentNews() {
				this.showNews = false
				store.set(this.newsStoreKey, true)
			},

		},
		mounted() {
			this.showNews = (this.hasNews && !this.hasSeenNews &&
				this.hasRequiredRole && this.isNewsTimely)
		},
	}
</script>
