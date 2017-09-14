<template>
	<div class="notification content" v-if="showNews">
		<button class="delete" @click="seenCurrentNews"></button>
		<p>Kiedy to się stało?! 12 tygodni pierwszej edycji kursu już za nami! Chcielibyśmy niezwykle podziękować Ci za zaangażowanie i pogratulować tak długiej i systematycznej nauki! Jesteśmy naprawdę pod wrażeniem! <wnl-emoji name="tada"/></p>

		<p>Na zakończenie kursu, prosimy Cię o wypełnienie ostatniej ankiety ewaluacyjnej. Część pytań na pewno rozpoznasz z poprzednich kwestionariuszy, ale tym razem skupimy się też na ocenie poszczególnych przedmiotów, w końcu możecie je porównać już między sobą. <wnl-emoji name="bar_chart"/></p>

		<p>Ankieta jest tym razem nieco dłuższa, ale wciąż nie powinna zająć więcej, niż 15 minut. <wnl-emoji name="wink"/></p>

		<p class="has-text-centered"><a class="button is-primary is-outlined is-small" target="_blank" href="https://goo.gl/forms/C1mQ0MUwUzZBJyTO2">Przejdź do ostatniej ankiety</a></p>

		<p>Dziękujemy i trzymamy za Ciebie kciuki!</p>
	</div>
</template>

<script>
	import store from 'js/services/messagesStore'
	import { mapGetters } from 'vuex'

	const CURRENT_NEWS = 'survey-end'
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
