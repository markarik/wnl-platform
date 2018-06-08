<template>
	<div class="notification content" v-if="showNews">
		<button class="delete" @click="seenCurrentNews"></button>

		<p class="strong">Cześć {{currentUserName}}! 👋</p>
		<p class="strong">Witaj na 3. edycji kursu "Więcej niż LEK",  w naszym wirtualnym pokoju nauki!</p>
		<p>W ciągu najbliższych miesięcy spędzisz tu sporo czasu, więc rozgość się i czuj jak u siebie. 😉</p>

		<p>Proponujemy zacząć od lekcji <a href="https://platforma.wiecejnizlek.pl/app/courses/1/lessons/16" target="_blank">Wstęp do kursu</a>, a zwłaszcza ekranu <a href="https://platforma.wiecejnizlek.pl/app/courses/1/lessons/16/screens/82" target="_blank">Obsługa platformy</a>. Tam w kilku krótkich filmach zapoznasz się ze wszystkimi najważniejszymi funkcjami platformy, a przy okazji dowiesz się sporo o konstrukcji kursu. 🙂</p>

		<p class="has-text-centered">
			<a class="button is-primary is-outlined" target="_blank" href="https://platforma.wiecejnizlek.pl/app/courses/1/lessons/16">Odwiedź Wstęp do kursu</a>
		</p>

		<p>Jedną z najważniejszych funkcji jest teraz pewnie dla Ciebie <a href="https://platforma.wiecejnizlek.pl/app/myself/availabilities" target="_blank">Plan pracy</a>. 😉 To tu możesz zdefiniować swój własny harmonogram nauki i zacząć ją... już od dziś! 🎉 Na ekranie <a href="https://platforma.wiecejnizlek.pl/app/courses/1/lessons/16/screens/82" target="_blank">Obsługa platformy</a> znajdziesz również film poświęcony temu narzędziu. 🙂</p>

		<p><strong>Co ważne! Pracujemy wciąż nad nowymi prezentacjami z Pulmonologii oraz Endokrynologii</strong>. Na pewno pojawią się na platformie przed oficjalnym startem kursu. 🙂 Możecie spokojnie jednak korzystać z istniejących prezentacji - są one kompletene i służyły dwóm poprzednim edycjom. Nowe będą po prostu lepiej zorganizowane. 😉</p>

		<p>W razie pytań jesteśmy dostępni cały dzień na platformie i bedzięmy rozwiązywać wszelkie zagwozdki. 🙂</p>

		<p>Życzymy powodzenia i owocnej pracy z kursem!</p>

		<p>Z serdecznymi pozdrowieniami,</p>

		<p style="font-style: italic;">Ekipa Więcej niż LEK</p>
	</div>
</template>

<script>
	import store from 'js/services/messagesStore'
	import { mapGetters } from 'vuex'

	const CURRENT_NEWS = 'edition-3-course-begins'
	const DISPLAY_FROM = new Date('2018-06-09 03:00:00')
	const DISPLAY_UNTIL = ''
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
				return store.get(this.newsStoreKey)
			},
			isNewsTimely() {
				let now = new Date()
				return (DISPLAY_FROM === '' || DISPLAY_FROM < now) &&
				(DISPLAY_UNTIL === '' || DISPLAY_UNTIL > now)
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
