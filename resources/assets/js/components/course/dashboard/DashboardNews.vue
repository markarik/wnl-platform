<template>
	<div class="notification content" v-if="showNews">
		<button class="delete" @click="seenCurrentNews"></button>

		<p class="strong">Cześć!</p>
		<p>Nieuchronnie nastał oficjalny koniec 2. edycji kursu "Więcej niż LEK"! Oczywiście zostajemy z Tobą do samego egzaminu i mocno trzymamy za Ciebie kciuki! <wnl-emoji name="+1"/></p>

		<p>Jak wiesz, robienie przerw jest ważne z punktu widzenia higieny umysłowej. Zachęcamy zatem do tego, aby jedną z nich wykorzystać na podzielenie się z nami, już po raz ostatni, refleksjami na temat kursu. <wnl-emoji name="wink"/></p>

		<p>W ostatniej ankiecie ewaluacyjnej możesz ocenić wszystkie grupy prezentacji, finalnie odnieść się do poszczególnych aspektów kursu oraz standardowo podzielić się z nami własnymi uwagami. <wnl-emoji name="mega"/></p>

		<p>Każda ocena jest dla nas niezwykle cenna, ponieważ pokazuje nam pełniejszy obraz Waszej oceny kursu, który tworzymy dla Waszej satysfakcji. <wnl-emoji name="raised_hands"/></p>

		<p class="has-text-centered">Możemy na Ciebie liczyć, prawda? <wnl-emoji name="wink"/></p>

		<p class="has-text-centered">
			<a class="button is-primary is-outlined" target="_blank" href="https://goo.gl/forms/9GEu3xmj3mWiY0xf2">Wypełnij ostatnią ankietę</a>
		</p>
	</div>
</template>

<script>
	import store from 'js/services/messagesStore'
	import { mapGetters } from 'vuex'

	const CURRENT_NEWS = 'edition-2-last-survey'
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
