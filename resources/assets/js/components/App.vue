<template>
	<div id="app" v-if="!isCurrentUserLoading">
		<div class="wnl-overlay" v-if="shouldDisplayOverlay">
			<span class="loader"></span>
			<span class="loader-text">Uwaga, nadjeżdża wiedza...</span>
		</div>
		<wnl-navbar :show="true"></wnl-navbar>
		<wnl-global-notification/>
		<div class="wnl-main">
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
	// Import global components
	import store from 'store'
	import Navbar from 'js/components/global/Navbar.vue'
	import GlobalNotification from 'js/components/global/GlobalNotification.vue'
	import { mapGetters, mapActions } from 'vuex'
	import axios from 'axios';
	import {getApiUrl} from 'js/utils/env';
	import sessionStore from 'js/services/sessionStore';

	export default {
		name: 'App',
		components: {
			'wnl-navbar': Navbar,
			'wnl-global-notification': GlobalNotification
		},
		computed: {
			...mapGetters(['currentUserId', 'isCurrentUserLoading', 'shouldDisplayOverlay', 'currentUserRoles']),
		},
		methods: {
			...mapActions([
				'resetLayout',
				'setActiveUsers',
				'setLayout',
				'setupCurrentUser',
				'toggleOverlay',
				'userJoined',
				'userLeft'
			]),
			...mapActions('notifications', ['initNotifications']),
			...mapActions('course', {
				courseSetup: 'setup',
				checkUserRoles: 'checkUserRoles',
			}),
		},
		mounted() {
			this.toggleOverlay({source: 'course', display: true})
			sessionStore.clearAll()

			Promise.all([this.setupCurrentUser(), this.courseSetup(1)])
				.then(() => {
					window.setInterval(() => {
						axios.put(getApiUrl(`users/${this.currentUserId}/state/time`))
					}, 1000 * 60 * 3)

					this.initNotifications()

					this.$router.afterEach((to) => {
						to.matched.some((record) => {
							!record.meta.keepsNavOpen && this.resetLayout()
						})
					})

					this.setLayout(this.$breakpoints.currentBreakpoint())
					this.$breakpoints.on('breakpointChange', (previousLayout, currentLayout) => {
						this.setLayout(currentLayout)
					})

					window.Echo.join('active-users')
						.here(users => this.setActiveUsers(users))
						.joining(user => this.userJoined(user))
						.leaving(user => this.userLeft(user))

					this.checkUserRoles(this.currentUserRoles)
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
