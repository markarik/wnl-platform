<template>
	<div class="notification content" v-if="showNews">
		<button class="delete" @click="seenCurrentNews"></button>
		<p class="strong">Cześć {{currentUserName}}!</p>
		<p>Już w tę sobotę odbędą się pierwsze warsztaty ze specjalistą w ramach kursu "Więcej niż LEK"! Pan <strong>dr&nbsp;hab.&nbsp;n.&nbsp;med.&nbsp;Mariusz&nbsp;Puszczewicz</strong> przygotowuje oczywiście własny program warsztatów, natomiast do dziś możesz zgłaszać też zagadnienia, na których omówieniu zależy Ci najbardziej. :)</p>

		<p>Zajrzyj na lekcję Warsztaty i w systemie pytań i odpowiedzi na ekranie Interna, zgłoś wybrane przez Ciebie tematy. To wyjątkowa szansa uzyskania wyczerpującej odpowiedzi od wyjątkowego specjalisty! Do zobaczenia! :)</p>

		<p class="has-text-centered">
			<a class="button is-primary is-outlined" href="https://platforma.wiecejnizlek.pl/app/courses/1/lessons/17/screens/97">
				Przejdź do warsztatów z Interny
			</a>
		</p>
	</div>
</template>

<script>
	import store from 'js/services/messagesStore'
	import { mapGetters } from 'vuex'

	const CURRENT_NEWS = 'workshops-deadline'
	const REQUIRED_ROLE = 'workshop-participant'

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
