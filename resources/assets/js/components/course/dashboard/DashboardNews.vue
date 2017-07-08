<template>
	<div class="notification content" v-if="showNews">
		<button class="delete" @click="seenCurrentNews"></button>
		<p class="strong">Cześć {{currentUserName}}!</p>
		<p>Już w tę sobotę odbędą się pierwsze warsztaty ze specjalistą w ramach kursu "Więcej niż LEK", które poprowadzi Pan <strong>dr&nbsp;hab.&nbsp;n.&nbsp;med.&nbsp;Mariusz&nbsp;Puszczewicz</strong>.</p>

		<p>Przypominamy, że zajęcia odbywają się w <strong>Collegium da Vinci</strong> przy ul. Gen. Tadeusza Kutrzeby 10 w Poznaniu, <strong>sala N205</strong>.</p>

		<p>
			<strong>Plan warsztatów:</strong><br>
			<strong>11:00 - 12:30</strong> - Wstęp do diagnostyki w Internie, z naciskiem na Reumatologię + Pytania i Odpowiedzi<br>
			<strong>12:30 - 14:00</strong> - Przerwa obiadowa<br>
			<strong>14:00 - 15:45</strong> - Przypadki kliniczne + Omówienie<br>
			<strong>15:45 - 16:00</strong> - Przerwa kawowa<br>
			<strong>16:00 - 17:00</strong> - Godzina z mentorem (może ulec przedłużeniu)<br>
		</p>

		<p>Wszystkie powyższe informacje znajdziesz też w lekcji Warsztaty / Interna. :) Do zobaczenia!</p>
	</div>
</template>

<script>
	import store from 'js/services/messagesStore'
	import { mapGetters } from 'vuex'

	const CURRENT_NEWS = ''
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
