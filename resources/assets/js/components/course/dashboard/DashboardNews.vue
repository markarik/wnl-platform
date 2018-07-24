<template>
	<div class="notification content" v-if="showNews">
		<button class="delete" @click="seenCurrentNews"></button>

		<p class="has-text-centered"><strong>PRZYPOMNIENIE</strong></p>

		<p class="strong">Cześć {{currentUserName}}! 👋</p>

		<p>Piszemy z krótkim przypomnieniem o tym, że upłynął termin płatności 3. raty za kurs. 😨 Jeżeli został już on przez Ciebie opłacony w całości - możesz zignorować i zamknąć tę wiadomość.</p>

		<p>Jeśli jednak jesteś jedną z osób, którym omsknęła się 3. wpłata - tymczasowo odblokowaliśmy dostęp do Twojego konta, aby nie hamować postępu w nauce. Prosimy jednak o wpłatę do 25 lipca. Po tej dacie niestety ponownie włączymy skrypt automatycznie zamykający dostępy do kont. 😔 Masz jednak sporo czasu na dokonanie wpłaty - ne pewno się wyrobisz. 😉</p>

		<p>Wszystkie szczegóły dotyczące płatności znajdziesz w zakładce <router-link :to="{name: 'my-orders'}">KONTO > Twoje zamówienia</router-link>.</p>

		<p>Życzymy powodzenia i owocnej pracy z kursem!</p>

		<p style="font-style: italic;">Ekipa Więcej niż LEK</p>
	</div>
</template>

<script>
	import store from 'js/services/messagesStore'
	import { mapGetters } from 'vuex'

	const CURRENT_NEWS = 'edition-3-instalments-announcement'
	const DISPLAY_FROM = '' // new Date() or empty string
	const DISPLAY_UNTIL = new Date('2018-07-26') // new Date() or empty string
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
