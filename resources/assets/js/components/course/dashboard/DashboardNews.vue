<template>
	<div class="notification content" v-if="showNews">
		<button class="delete" @click="seenCurrentNews"></button>

		<p class="has-text-centered"><strong>PROŚBA!</strong></p>

		<p class="strong">Cześć {{currentUserName}}! 👋</p>

		<p>Od pierwszej ankiety minęło już trochę czasu! Zgodnie z najlepszymi praktykami, oceniamy naszą pracę 3 razy w ciągu każdej edycji. To pozwala nam stwierdzić, czy dobrze planujemy pracę i rozwiązujemy najważniejsze z Waszych problemów. 🙂</p>

		<p>Dziś mamy prośbę o wypełnienie 2. ankiety, składającej się z 15 pytań. 😉 Pomóż nam uczynić kurs "Więcej niż LEK" lepszym!</p>

		<p class="has-text-centered margin vertical">
			<a class="button is-primary" target="_blank" href="https://docs.google.com/forms/d/e/1FAIpQLSf3NZDU7tbqPGMIIVLRFaVhDLBuC2lk-WSZdZmIkaticYYjSw/viewform">
				Wypełnij ankietę!
			</a>
		</p>

		<p>Życzymy powodzenia i owocnej pracy z kursem!</p>

		<p style="font-style: italic;">Ekipa Więcej niż LEK</p>
	</div>
</template>

<script>
	import store from 'js/services/messagesStore'
	import { mapGetters } from 'vuex'

	const CURRENT_NEWS = 'edition-3-survey-2-announcement'
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
