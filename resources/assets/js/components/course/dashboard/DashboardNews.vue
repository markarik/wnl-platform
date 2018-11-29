<template>
	<wnl-dashboard-news-content
			v-if="showNews"
			:message="dashboardNews.message"
			:slug="dashboardNews.slug"
			@onClose="seenCurrentNews"
	/>
</template>

<script>
	import store from 'js/services/messagesStore'
	import { mapGetters } from 'vuex'
	import WnlDashboardNewsContent from 'js/components/course/dashboard/DashboardNewsContent'

	export default {
		name: 'DashboardNews',
		data() {
			return {
				showNews: false
			}
		},
		components: {
			WnlDashboardNewsContent
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
