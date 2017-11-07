<template>
	<div class="notification content" v-if="showNews">
		<button class="delete" @click="seenCurrentNews"></button>

		<p>Serdecznie witamy Cię w wirtualnym pokoju nauki do LEK‑u! Oficjalnie rozpoczynamy 2. edycję kursu! <wnl-emoji name="tada"/></p>

		<p>Mamy nadzieję, że czujesz mobilizację i chęć do odkrycia nauki na nowo! Przed nami 4 miesiące wspólnej pracy. Zachęcamy Cię bardzo gorąco do uczestniczenia w dyskusjach na platformie, dzielenia się wiedzą oraz informowania nas o wszystkich problemach, czy wątpliwościach, z którymi przyjdzie Ci się spotkać. <wnl-emoji name="wink"/></p>

		<p>Zanim usiądziesz wygodnie, aby zmierzyć się z pierwszą lekcją - Kardiologia 1, zapraszamy Cię do zapoznania się z trzema krótkimi lekcjami wstępnymi (w grupie <strong>Więcej niż LEK</strong> w nawigacji po lewej stronie):
			<ul>
				<li><strong>Wstęp do kursu</strong>, czyli warto zacząć od podstaw. <wnl-emoji name="wink"/> Tutaj skupimy się na obsłudze platformy oraz celach kursu.</li>
				<li><strong>5 filarów "Więcej niż LEK"</strong> odpowiadających na 5 podstawowych problemów związanych z przygotowaniem do egzaminu. Pomogą Ci w pełni zrozumieć dlaczego tak, a nie inaczej zaprojektowaliśmy ten kurs.</li>
				<li><strong>Efektywna nauka</strong> to już lekcja w pełni poświęcona wiedzy o tym jak można uczyć się lepiej, szybciej i zapamiętywać trwalej.</li>
			</ul>
		</p>

		<p>W razie jakichkolwiek pytań możesz pisać do nas w Pomocy, na info@wiecejnizlek.pl lub na facebooku. <wnl-emoji name="wink"/></p>

		<p>Trzymamy za Ciebie kciuki i życzymy wiele radości z nauki! <wnl-emoji name="raised_hands"/></p>
	</div>
</template>

<script>
	import store from 'js/services/messagesStore'
	import { mapGetters } from 'vuex'

	const CURRENT_NEWS = 'course-start'
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
