<template>
	<div class="notification content" v-if="showNews">
		<button class="delete" @click="seenCurrentNews"></button>

		<p class="strong">Cześć! <wnl-emoji name="mega"/></p>
		<p>Za nami już ponad połowa kursu i jesteśmy bardzo ciekawi Waszych opinii! <wnl-emoji name="raised_hands"/></p>

		<p>Wasze odpowiedzi w pierwszej ankiecie były dla nas niezwykle pomocne. Pozwoliły nam między innymi na poprawienie nawigacji w prezentacjach, formatowania slajdów, czy zaplanowanie rozwoju platformy. <wnl-emoji name="wink"/></p>

		<p>Dziś ponownie prosimy Was o pomoc. Do końca kursu zostało jeszcze sporo czasu i chcielibyśmy jak najwięcej zrobić, aby odpowiedzieć na Wasze potrzeby i problemy! Będziemy wdzięczni, jeżeli poświęcisz kilka minut na odpowiedzenie na 15 krótkich pytań.
 <wnl-emoji name="+1"/></p>

		<p class="has-text-centered">
			<a class="button is-primary is-outlined" target="_blank" href="https://goo.gl/forms/WYm96EmCTYDGImjB2">Wypełnij ankietę</a>
		</p>
	</div>
</template>

<script>
	import store from 'js/services/messagesStore'
	import { mapGetters } from 'vuex'

	const CURRENT_NEWS = 'edition-2-first-survey'
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
