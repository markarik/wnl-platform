<template>
	<div class="notification content" v-if="showNews">
		<button class="delete" @click="seenCurrentNews"></button>

		<p class="has-text-centered"><strong>WITAJ NA KURSIE!</strong></p>

		<p>Cześć! 👋</p>

		<p>Zanim rozpoczniesz wcześniejszą naukę, ustaw swój własny plan pracy!
		Możesz to zrobić tutaj:</p>

		<p class="aligncenter">
			<a :href="planLink" target="_blank" class="button is-primary">
				Plan pracy
			</a>
		</p>

		<p>W zakładce znajdziesz również film z instrukcją jak to zrobić. 🙂</p>

		<p>Możesz także poczekać na oficjalny start kursu 5 listopada, wtedy pierwsza lekcja otworzy się automatycznie!</p>

		<p>Miłej nauki! 🚀</p>

		<p style="font-style: italic;">Ekipa Więcej niż LEK</p>
	</div>
</template>

<script>
	import store from 'js/services/messagesStore'
	import { mapGetters } from 'vuex'
	import { getUrl } from 'js/utils/env'

	const CURRENT_NEWS = 'edition-4-plan-redirect'
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
			planLink() {
				return getUrl('app/myself/availabilities')
			}
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
