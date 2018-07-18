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
	import axios from 'axios';
	import store from 'store'
	import { mapGetters, mapActions } from 'vuex'
	import { isEmpty } from 'lodash'

	import Navbar from 'js/components/global/Navbar.vue'
	import Alerts from 'js/components/global/GlobalAlerts'
	import sessionStore from 'js/services/sessionStore';
	import {getApiUrl} from 'js/utils/env';
	import {startTracking} from 'js/services/activityMonitor';
	import {SOCKET_EVENT_USER_SENT_MESSAGE} from 'js/plugins/socket'

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
				'getSetting'
			]),
			currentOverlayText() {
				return !isEmpty(this.overlayTexts) ? this.overlayTexts[0] : this.$t('ui.loading.default')
			},
		},
		methods: {
			...mapActions([
				'resetLayout',
				'setLayout',
				'setupCurrentUser',
				'toggleOverlay'
			]),
			...mapActions('users', ['userJoined', 'userLeft', 'setActiveUsers']),
			...mapActions('notifications', ['initNotifications']),
			...mapActions('chatMessages', ['fetchUserRoomsWithMessages', 'onNewMessage', 'setConnectionStatus', 'updateFromEventLog']),
			...mapActions('tasks', ['initModeratorsFeedListener']),
			...mapActions('course', { courseSetup: 'setup' }),
		},
		mounted() {
			this.toggleOverlay({source: 'course', display: true})
			sessionStore.clearAll()

			return this.setupCurrentUser()
				.then(() => {
					if (this.getSetting('thick_scrollbar')) {
						console.log(document.documentElement.style);
					}
					this.setConnectionStatus(false)
					// Setup Notifications
					this.initNotifications()
					this.currentUserRoles.indexOf('moderator') > -1 && this.initModeratorsFeedListener()

					// Setup Chat
					const userChannel = `authenticated-user`
					this.fetchUserRoomsWithMessages({page: 1})
						.then((pointer) => this.$socketJoinRoom(userChannel, pointer))
						.then((data) => {
							this.updateFromEventLog(data.events)
							this.setConnectionStatus(true)
							this.$socketRegisterListener(SOCKET_EVENT_USER_SENT_MESSAGE, this.onNewMessage)
						})

					// Setup time tracking
					startTracking(this.currentUserId);

					this.$router.afterEach((to) => {
						!to.params.keepsNavOpen && this.resetLayout()
					})

					// Setup active users
					window.Echo.join('active-users')
						.here(users => this.setActiveUsers({users, channel: 'activeUsers'}))
						.joining(user => this.userJoined({user, channel: 'activeUsers'}))
						.leaving(user => this.userLeft({user, channel: 'activeUsers'}))

					this.setLayout(this.$breakpoints.currentBreakpoint())
					this.$breakpoints.on('breakpointChange', (previousLayout, currentLayout) => {
						this.setLayout(currentLayout)
					})

					return this.courseSetup(1)
				})
				.then(() => {
					this.toggleOverlay({source: 'course', display: false})
				})
				.catch(error => {
					$wnl.logger.error(error)
					this.toggleOverlay({source: 'course', display: false})
				})
		},
		watch: {
			'$route' (to, from) {
				window.axios.defaults.headers.common['X-BETHINK-LOCATION'] = window.location.href;
			}
		},
	}
</script>
