<template>
	<div class="notification content" v-if="showNews">
		<button class="delete" @click="seenCurrentNews"></button>
		<p class="strong">Cześć {{currentUserName}}!</p>

		<p>Nagrania z warsztatów z Interny są już dostępne! Wrzuciliśmy je na YouTube jako prywatne nagrania. <wnl-emoji name="tv"/></p>

		<p>Aby uzyskać do nich dostęp, napiszcie do nas na warsztaty@wiecejnizlek.pl lub na facebooku wysyłając swój <strong>adres e-mail, którego używacie na YouTube</strong>. Inne maile niestety nie zadziałają, sprawdziliśmy to. <wnl-emoji name="wink"/></p>

		<p>Do zobaczenia!</p>
	</div>
</template>

<script>
	import store from 'js/services/messagesStore'
	import { mapGetters } from 'vuex'

	const CURRENT_NEWS = 'workshop-movie'
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
