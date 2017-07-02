<template>
	<div class="notification content" v-if="showNews">
		<button class="delete" @click="seenCurrentNews"></button>
		<p class="strong">Cześć!</p>
		<p>Większość z Was zakończyła już pierwszy tydzień nauki z kursem "Więcej niż LEK"! Usłyszeliśmy od Was wiele dobrych słów na temat kursu, oraz wiele fantastycznych, krytycznych uwag. Wszystkie bardzo pomagają nam każdego dnia poprawiać jakość kursu i podnosić jego wartość dla Was. Jednak im więcej będziemy mieli wskazówek, tym większa szansa, że kurs będzie ewoluował w dobrym kierunku.</p>

		<p>Dlatego prosimy o odpowiedzenie na <strong>10 krótkich pytań</strong>, które pozwolą nam trafniej ocenić, jak możemy odpowiedzieć na Wasze potrzeby (zajmie Ci to najwyżej 5 minut). :)</p>

		<p class="has-text-centered">
			<a class="button is-primary is-outlined" target="_blank" href="https://goo.gl/forms/o9HtgPTtLbpABHRo2">Wypełnij krótką ankietę</a>
		</p>
	</div>
</template>

<script>
	import store from 'js/services/messagesStore'

	const CURRENT_NEWS = ''

	export default {
		name: 'DashboardNews',
		data() {
			return {
				showNews: false
			}
		},
		computed: {
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
			if (CURRENT_NEWS !== '' && !store.get(this.newsStoreKey)) {
				this.showNews = true
			}
		},
	}
</script>
