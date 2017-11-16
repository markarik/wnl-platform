<template>
	<div class="notification content" v-if="showNews">
		<button class="delete" @click="seenCurrentNews"></button>

		<p class="strong">Cześć! <wnl-emoji name="mega"/></p>
		<p>Większość z Was zakończyła już pierwszy tydzień nauki z kursem "Więcej niż LEK"! <wnl-emoji name="raised_hands"/></p>

		<p>Usłyszeliśmy od Was wiele dobrych słów na temat kursu, oraz wiele fantastycznych, krytycznych uwag. Wszystkie bardzo pomagają nam każdego dnia poprawiać jakość kursu i podnosić jego wartość dla Was. Jednak im więcej będziemy mieli wskazówek, tym większa szansa, że kurs będzie ewoluował w dobrym kierunku. <wnl-emoji name="wink"/></p>

		<p>Dlatego prosimy, odpowiedz na <strong>15 krótkich pytań</strong>, które pozwolą nam trafniej ocenić, jak możemy odpowiedzieć na Wasze potrzeby. <wnl-emoji name="+1"/></p>

		<p class="has-text-centered">
			<a class="button is-primary is-outlined" target="_blank" href="https://goo.gl/forms/Ym8opY88cu3QT31M2">Wypełnij krótką ankietę</a>
		</p>

		<p></p>
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
