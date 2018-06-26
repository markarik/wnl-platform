<template>
	<div class="notification content" v-if="showNews">
		<button class="delete" @click="seenCurrentNews"></button>

		<p class="has-text-centered"><strong>OGŁOSZENIE</strong></p>

		<p class="strong">Cześć {{currentUserName}}! 👋</p>

		<p>Pierwsze tygodnie kursu już za nami! 🎉</p>

		<p>Usłyszeliśmy od Was wiele dobrych słów na temat kursu, oraz wiele fantastycznych, krytycznych uwag. Wszystkie bardzo pomagają nam każdego dnia poprawiać jakość kursu i podnosić jego wartość dla Was. 🙂</p>

		<p>Jednak im więcej będziemy mieli wskazówek, tym większa szansa, że kurs będzie ewoluował w dobrym kierunku. Dlatego prosimy Cię bardzo o odpowiedzenie na kilka krótkich pytań, które pozwolą nam trafniej ocenić, jak możemy odpowiedzieć na Wasze potrzeby. 😉</p>

		<p class="has-text-centered margin bottom">
			<a class="button is-primary" href="https://goo.gl/forms/fO8WQC5szHDSWFa13">
				Wypełnij ankietę
			</a>
		</p>

		<p class="strong">Ważna informacja! W zakładce KONTO > Twoje zamówienia znajdziesz wszystkie faktury wystawione do Twoich zamówień, a w zakładce KONTO > Certyfikaty - certyfikat uczestnictwa w kursie.</p>

		<p>Życzymy powodzenia i owocnej pracy z kursem!</p>

		<p style="font-style: italic;">Ekipa Więcej niż LEK</p>
	</div>
</template>

<script>
	import store from 'js/services/messagesStore'
	import { mapGetters } from 'vuex'

	const CURRENT_NEWS = 'edition-3-survey-1'
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
