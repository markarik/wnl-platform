<template>
	<div class="notification content" v-if="showNews">
		<button class="delete" @click="seenCurrentNews"></button>
		<p class="has-text-centered"><strong v-html="dashboardNews.slug"></strong></p>
		<span v-html="dashboardNews.message"></span>
	</div>
</template>

<script>
	import store from 'js/services/messagesStore'
	import { mapGetters } from 'vuex'
	import { getUrl } from 'js/utils/env'

	export default {
		name: 'DashboardNews',
		data() {
			return {
				showNews: false
			}
		},
		computed: {
			...mapGetters(['currentUserName', 'hasRole']),
			...mapGetters('siteWideMessages', ['dashboardNews']),
			hasSeenNews() {
				return !!store.get(this.newsStoreKey)
			},
			newsStoreKey() {
				return `seen-dashboard-news-${this.dashboardNews.id}`
			},
		},
		methods: {
			seenCurrentNews() {
				this.showNews = false
				store.set(this.newsStoreKey, true)
			},

		},
		mounted() {
			this.showNews = this.dashboardNews && !this.hasSeenNews;
		},
	}
</script>
