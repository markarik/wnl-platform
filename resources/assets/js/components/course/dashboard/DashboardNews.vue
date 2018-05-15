<template>
	<div class="notification content" v-if="showNews">
		<button class="delete" @click="seenCurrentNews"></button>

		<p class="strong">Cześć {{currentUserName}}! 👋</p>
		<p class="strong">Witaj na 3. edycji kursu "Więcej niż LEK",  w naszym wirtualnym pokoju nauki!</p>
		<p>W ciągu najbliższych miesięcy spędzisz tu sporo czasu, więc rozgość się i czuj jak u siebie. 😉</p>

		<p>Proponujemy zacząć od lekcji <a href="https://platforma.wiecejnizlek.pl/app/courses/1/lessons/16" target="_blank">Wstęp do kursu</a>, a zwłaszcza ekranu <a href="https://platforma.wiecejnizlek.pl/app/courses/1/lessons/16/screens/82" target="_blank">Obsługa platformy</a>. Tam w kilku krótkich filmach zapoznasz się ze wszystkimi najważniejszymi funkcjami platformy, a przy okazji dowiesz się sporo o konstrukcji kursu. 🙂</p>

		<p class="has-text-centered">
			<a class="button is-primary is-outlined" target="_blank" href="https://goo.gl/forms/9GEu3xmj3mWiY0xf2">Odwiedź Wstęp do kursu</a>
		</p>

		<p>Jedną z najważniejszych funkcji jest teraz pewnie dla Ciebie <a href="https://platforma.wiecejnizlek.pl/app/myself/availabilities" target="_blank">Plan pracy</a>. 😉 To tu możesz zdefiniować swój własny harmonogram nauki i zacząć ją... już od dziś! 🎉 Na ekranie <a href="https://platforma.wiecejnizlek.pl/app/courses/1/lessons/16/screens/82" target="_blank">Obsługa platformy</a> znajdziesz również film poświęcony temu narzędziu. 🙂</p>

		<p>W razie pytań jesteśmy dostępni cały dzień na platformie i bedzięmy rozwiązywać wszelkie zagwozdki. 🙂</p>

		<p>Życzymy powodzenia i owocnej pracy z kursem!</p>

		<p>Z serdecznymi pozdrowieniami,</p>

		<p style="font-style: italic;">Ekipa Więcej niż LEK</p>
	</div>
</template>

<script>
	import store from 'js/services/messagesStore'
	import { mapGetters } from 'vuex'

	const CURRENT_NEWS = 'edition-3-welcome-dude'
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
			newsStoreKey() {
				return `seen-dashboard-news-${CURRENT_NEWS}`
			}
		},
		methods: {
			seenCurrentNews() {
				this.showNews = false
				store.set(this.newsStoreKey, true)
			},
		},
		mounted() {
			if (CURRENT_NEWS !== '' &&
				!store.get(this.newsStoreKey) &&
				(REQUIRED_ROLE === '' || this.hasRole(REQUIRED_ROLE))
			) {
				this.showNews = true
			}
		},
	}
</script>
