<template>
	<div class="notification content" v-if="showNews">
		<button class="delete" @click="seenCurrentNews"></button>

		<p class="has-text-centered"><strong>OSTATNIA ANKIETA</strong></p>

		<p>Cześć! 👋</p>

		<p>Zakończyliśmy już oficjalnie 3. edycję kursu! Dziękujemy Ci baaardzo za zaufanie oraz zaangażowanie - bez Ciebie ten kurs nie działałby tak skutecznie!</p>

		<p>Prosimy przy tej okazji o pozostawienie <a href="https://www.facebook.com/wiecejnizlek/reviews" target="_blank">recenzji na facebooku</a> - wiele osób czeka na Twoją opinię. 🙂</p>

		<p>Na koniec kursu przeprowadzamy też zawsze ostatnią ankietę ewaluacyjną. Jest ona dla nas najważniejsza z wszystkich trzech, ponieważ wypełniając ją posiadasz już pełen obraz działania kursu. Prosimy, poświęć chwilę na udzielenie nam informacji zwrotnej.</p>

		<p class="aligncenter">
			<a href="https://goo.gl/forms/PBLnL8WkQqtPKKYI3" target="_blank" class="button is-primary">
				Wypełnij ankietę
			</a>
		</p>

		<p>Na koniec przypominamy, że jeśli planujesz uczestnictwo w kolejnej edycji kursu, możesz wciąż zarezerwować sobie miejsce. 🙂 Dzięki temu możesz spokojnie zapisać się do 15 października, a miejsce będzie na Ciebie czekać. Rezerwacja natomiast nie jest zobowiązująca. 🙂</p>

		<p class="aligncenter">
			<a href="https://wiecejnizlek.pl/zostaw-e-mail" target="_blank" class="button is-primary is-outlined">
				Zarezerwuj miejsce na kursie
			</a>
		</p>

		<p>Życzymy powodzenia na ostatniej prostej i pozostajemy do dyspozycji!</p>

		<p style="font-style: italic;">Ekipa Więcej niż LEK</p>
	</div>
</template>

<script>
	import store from 'js/services/messagesStore'
	import { mapGetters } from 'vuex'

	const CURRENT_NEWS = 'edition-3-last-survey'
	const DISPLAY_FROM = '' // new Date() or empty string
	const DISPLAY_UNTIL = '' // new Date() or empty string
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
