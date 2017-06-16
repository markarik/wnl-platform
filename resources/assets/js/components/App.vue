<template>
	<div id="app" v-if="!isCurrentUserLoading">
		<wnl-navbar :show="true"></wnl-navbar>
		<div class="wnl-main">
			<router-view></router-view>
		</div>
	</div>
</template>

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
			...mapGetters(['currentUserId', 'isCurrentUserLoading'])
		},
		methods: {
			...mapActions(['setupCurrentUser', 'setLayout', 'resetLayout']),
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
			this.$router.afterEach((to) => {
				to.matched.some((record) => {
					if (!record.meta.keepsNavOpen) {
						this.resetLayout()
					}
				})
			})

			this.setLayout(this.$breakpoints.currentBreakpoint())
			this.$breakpoints.on('breakpointChange', (previousLayout, currentLayout) => {
				this.setLayout(currentLayout)
			})
		}
	}
</script>
