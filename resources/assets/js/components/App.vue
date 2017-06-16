<template>
	<div id="app" v-if="!isCurrentUserLoading">
		<div class="wnl-overlay" v-if="isOverlayVisible">
			<span class="loader"></span>
		</div>
		<wnl-navbar :show="true"></wnl-navbar>
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
		justify-content: center
		left: 0
		position: fixed
		right: 0
		top: 0
		z-index: $z-index-overlay

		.loader
			height: 40px
			width: 40px
</style>

<script>
	// Import global components
	import store from 'store'
	import Navbar from 'js/components/global/Navbar.vue'
	import { mapGetters, mapActions } from 'vuex'

	const CACHE_VERSION = 1

	export default {
		name: 'App',
		components: {
			'wnl-navbar': Navbar
		},
		computed: {
			...mapGetters(['currentUserId', 'isCurrentUserLoading', 'isOverlayVisible'])
		},
		methods: {
			...mapActions(['setupCurrentUser', 'setLayout', 'resetLayout', 'toggleOverlay']),
			setupNotifications() {
				Echo.private(`user.${this.currentUserId}`)
						.listen('.App.Notifications.Events.LiveNotificationCreated', (notification) => {
							$wnl.logger.debug('Notification', notification);
						});
			},
		},
		created: function () {
			this.setupCurrentUser()
			//.then(()=>this.setupNotifications())
		},
		mounted() {
			this.$router.afterEach(() => {
				this.resetLayout()
			})

			this.setLayout(this.$breakpoints.currentBreakpoint())
			this.$breakpoints.on('breakpointChange', (previousLayout, currentLayout) => {
				this.setLayout(currentLayout)
			})
		}
	}
</script>
