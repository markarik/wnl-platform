<template>
	<div class="notification content" v-if="showNews">
		<button class="delete" @click="seenCurrentNews"></button>
		<p>Za nami już ponad połowa kursu i jesteśmy bardzo ciekawi Waszych opinii! Wasze odpowiedzi w pierwszej ankiecie były dla nas niezwykle pomocne. <wnl-emoji name="bar_chart"/></p>

		<p>Dziś ponownie prosimy Cię o pomoc. Do końca kursu zostało jeszcze sporo czasu i chcielibyśmy jak najwięcej zrobić, aby odpowiedzieć na Wasze potrzeby i problemy. Będziemy wdzięczni, jeżeli poświęcisz kilka minut na odpowiedzenie na 15&nbsp;krótkich&nbsp;pytań.&nbsp;<wnl-emoji name="wink"/></p>

		<p class="has-text-centered"><a class="button is-primary is-outlined is-small" href="https://goo.gl/forms/myBvbDblkFiE4r623">Przejdź do ankiety</a></p>

		<p>Dziękujemy!</p>
	</div>
</template>

<script>
	import store from 'js/services/messagesStore'
	import { mapGetters } from 'vuex'

	const CURRENT_NEWS = 'survey-2'
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
