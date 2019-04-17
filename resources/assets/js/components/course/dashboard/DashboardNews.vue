<template>
	<wnl-dashboard-news-content
		v-if="showNews"
		:message="dashboardNews.message"
		:message-arguments="messageArguments"
		:slug="dashboardNews.slug"
		@onClose="closed"
		@onContentClick="contentClicked"
	/>
</template>

<script>
import { mapGetters } from 'vuex';
import store from 'js/services/messagesStore';
import WnlDashboardNewsContent from 'js/components/course/dashboard/DashboardNewsContent';
import dashboardNewsMessageArguments from 'js/mixins/dashboard-news-message-arguments';
import context from 'js/consts/events_map/context.json';

export default {
	name: 'DashboardNews',
	mixins: [dashboardNewsMessageArguments],
	data() {
		return {
			featureContext: context.dashboard.features.news_message,
			showNews: false
		};
	},
	components: {
		WnlDashboardNewsContent
	},
	computed: {
		...mapGetters('siteWideMessages', ['dashboardNews']),
		hasSeenNews() {
			return !!store.get(this.newsStoreKey);
		},
		newsStoreKey() {
			return `seen-dashboard-news-${this.dashboardNews.id}`;
		},
	},
	methods: {
		closed() {
			this.track(this.featureContext.actions.close.value);
			this.showNews = false;
			store.set(this.newsStoreKey, true);
		},
		contentClicked({ target }) {
			const href = target.getAttribute('href');

			if (target && href) {
				this.track(this.featureContext.actions.click_link.value);

				if (/^https?:\/\//.test(href)) {
					// External links always open in a new tab
					event.preventDefault();
					window.open(target.href);
				} else {
					// Internal links
					const { altKey, ctrlKey, metaKey, shiftKey, button, defaultPrevented } = event;

					// don't handle with control keys
					if (metaKey || altKey || ctrlKey || shiftKey) return;
					// don't handle when preventDefault called
					if (defaultPrevented) return;
					// don't handle right clicks
					if (button !== undefined && button !== 0) return;
					// don't handle if `target="_blank"`
					if (target && target.getAttribute) {
						const linkTarget = target.getAttribute('target');
						if (/\b_blank\b/i.test(linkTarget)) return;
					}
					// don't handle same page links/anchors
					const url = new URL(target.href);
					const to = url.pathname;

					if (window.location.pathname !== to) {
						event.preventDefault();
						this.$router.push(to);
					}
				}
			}
		},
		track(action) {
			this.$trackUserEvent({
				feature: this.featureContext.value,
				action,
				target: this.dashboardNews.id,
				context: context.dashboard.value,
			});
		},
	},
	mounted() {
		this.showNews = this.dashboardNews && !this.hasSeenNews;

		if (this.showNews) {
			this.track(this.featureContext.actions.open.value);
		}
	},
};
</script>
