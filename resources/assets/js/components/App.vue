<template>
	<div id="app" v-if="!isCurrentUserLoading" :class="{'modal-active': modalVisible}">
		<div class="wnl-overlay" v-if="shouldDisplayOverlay">
			<span class="loader"></span>
			<span class="loader-text">{{currentOverlayText}}</span>
		</div>
		<wnl-navbar :show="true"></wnl-navbar>
		<div class="wnl-main">
			<wnl-alerts :alerts="alerts"/>
			<router-view></router-view>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

	.wnl-overlay
		align-items: center
		background: rgba(255, 255, 255, 0.9)
		bottom: 0
		display: flex
		flex-direction: column
		justify-content: center
		left: 0
		position: fixed
		right: 0
		top: 0
		z-index: $z-index-overlay

		.loader
			height: 40px
			width: 40px

		.loader-text
			margin-top: $margin-small
			color: $color-ocean-blue
</style>

<script>
import { mapGetters, mapActions } from 'vuex';
import { isEmpty } from 'lodash';

import Navbar from 'js/components/global/Navbar.vue';
import Alerts from 'js/components/global/GlobalAlerts';
import sessionStore from 'js/services/sessionStore';
import {startActivityTracking} from 'js/services/activityMonitor';
import {getApiUrl} from 'js/utils/env';

export default {
	name: 'App',
	components: {
		'wnl-navbar': Navbar,
		'wnl-alerts': Alerts,
	},
	computed: {
		...mapGetters([
			'currentUserId',
			'currentUserRoles',
			'isCurrentUserLoading',
			'overlayTexts',
			'shouldDisplayOverlay',
			'alerts',
			'modalVisible',
			'thickScrollbar',
		]),
		...mapGetters('siteWideMessages', ['siteWideAlerts']),
		currentOverlayText() {
			return !isEmpty(this.overlayTexts) ? this.overlayTexts[0] : this.$t('ui.loading.default');
		}
	},
	methods: {
		...mapActions([
			'resetLayout',
			'setLayout',
			'setupCurrentUser',
			'toggleOverlay',
			'addAlert'
		]),
		...mapActions('siteWideMessages', ['fetchUserSiteWideMessages', 'updateSiteWideMessage']),
		...mapActions('users', ['userJoined', 'userLeft', 'setActiveUsers']),
		...mapActions('notifications', ['initNotifications']),
		...mapActions('tasks', ['initModeratorsFeedListener']),
		...mapActions('course', { courseSetup: 'setup' }),
		...mapActions('products', ['fetchCurrentProducts']),
		handleSiteWideMessages() {
			this.siteWideAlerts.forEach(alert => {
				this.addAlert({
					text: this.$t(alert.message),
					type: 'info',
					dismissCallback: () => {
						this.updateSiteWideMessage(alert.id);
					}
				});
			});
		}
	},
	mounted() {
		this.toggleOverlay({source: 'course', display: true});
		sessionStore.clearAll();

		// Setup current products
		this.fetchCurrentProducts();

		return this.setupCurrentUser()
			.then(() => {
				// Setup Notifications
				this.initNotifications();
				this.currentUserRoles.indexOf('moderator') > -1 && this.initModeratorsFeedListener();
				this.fetchUserSiteWideMessages().then(() => {
					this.handleSiteWideMessages();
				});

				// Setup Chat
				this.$socketChatSetup();

				// Setup time tracking
				const activitiesConfig = {
					sadActivity: {
						incrementBy: 1000 * 60,
						inactivityTime: 1000 * 60,
						handle: this.$trackUserActivity,
					},
					activityMonitor: {
						incrementBy: 1000 * 60 * 10,
						inactivityTime: 1000 * 60 * 30,
						handle: userId => axios.put(getApiUrl(`users/${userId}/state/time`)),
					},
				};
				startActivityTracking(this.currentUserId, activitiesConfig);

				this.$router.afterEach((to) => {
					!to.params.keepsNavOpen && this.resetLayout();
				});

				// Setup active users
				window.Echo.join('active-users')
					.here(users => this.setActiveUsers({users, channel: 'activeUsers'}))
					.joining(user => this.userJoined({user, channel: 'activeUsers'}))
					.leaving(user => this.userLeft({user, channel: 'activeUsers'}));

				this.setLayout(this.$breakpoints.currentBreakpoint());
				this.$breakpoints.on('breakpointChange', (previousLayout, currentLayout) => {
					this.setLayout(currentLayout);
				});

				return this.courseSetup(1);
			})
			.then(() => {
				this.toggleOverlay({source: 'course', display: false});
			})
			.catch(error => {
				$wnl.logger.error(error);
				this.toggleOverlay({source: 'course', display: false});
			});
	},
	watch: {
		'$route' (to, from) {
			window.axios.defaults.headers.common['X-BETHINK-LOCATION'] = window.location.href;
			this.$trackUrlChange({
				value: window.location.href
			});
		},
		'thickScrollbar' (newVal) {
			if (newVal) {
				document.documentElement.classList.add('thick-scrollbar');
			} else {
				document.documentElement.classList.remove('thick-scrollbar');
			}
		}
	},
};
</script>
