<template>
	<div class="notification content" v-if="showNews">
		<button class="delete" @click="seenCurrentNews"></button>

		<p class="has-text-centered"><strong>WAŻNE! PRACE SERWISOWE 10 GRUDNIA 🛠</strong></p>

		<p class="strong">Cześć {{currentUserName}}! 👋</p>

		<p class="strong">10 grudnia od 10:00 do 12:00 będziemy prowadzili na platformie prace serwisowe. ⚙️</p>

		<p>W najlepszym wypadku nawet ich nie zauważycie, ale w najgorszym platforma w ciągu tych 2 godzin może być tymczasowo niedostępna.</p>

		<p>Sugerujemy zaplanowanie nauki tego dnia po godzinie 12:00. 🙂 Dziękujemy za wyrozumiałość!</p>

		<p style="font-style: italic;">Nerdy Więcej niż LEK</p>
	</div>
</template>

<script>
	import store from 'js/services/messagesStore'
	import { mapGetters } from 'vuex'
	import { getUrl } from 'js/utils/env'

	const CURRENT_NEWS = '4th-edition-loadbalancer-announcement'
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
