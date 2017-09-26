<template>
	<div class="wnl-app-layout">
		<wnl-sidenav-slot
			:isVisible="isSidenavVisible"
			:isDetached="!isSidenavMounted"
		>
			<wnl-main-nav :isHorizontal="!isSidenavMounted"></wnl-main-nav>
		</wnl-sidenav-slot>
		<div class="wnl-middle wnl-app-layout-main" :class="{'full-width': isMobileProfile, 'mobile-main': isMobileProfile}">
			<wnl-user-profile v-if="responseCondition" :response="response"></wnl-user-profile>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.myself-sidenav
		flex: 1

	.wnl-sidenav
		flex: 1
		padding: 7px 0

		&.mobile
			padding: 0

	.mobile-main
		overflow-y: auto
</style>

<script>
	import { mapActions, mapGetters } from 'vuex'

	import MainNav from 'js/components/MainNav'
	import UserProfile from 'js/components/user/UserProfile'
	import SidenavSlot from 'js/components/global/SidenavSlot'
	import { isProduction, getApiUrl } from 'js/utils/env'

	export default {
		name: 'Themselves',
		components: {
			'wnl-main-nav': MainNav,
			'wnl-user-profile': UserProfile,
			'wnl-sidenav-slot': SidenavSlot,
		},
		props: ['view'],
		data() {
			return {
				param: this.$route.params.userId,
				response: {},
			}
		},
		computed: {
			...mapGetters(['isSidenavMounted', 'isSidenavVisible', 'isMobileProfile']),
			isProduction() {
				return isProduction()
			},
			responseCondition() {
				return !_.isEmpty(this.response)
			}
		},
		methods: {
			...mapActions(['killChat']),
			goToDefaultRoute() {
				if (!this.view) {
					this.$router.replace({ name: 'my-orders' })
				}
			}
		},
		mounted() {
            axios.get(getApiUrl(`users/${this.param}/profile`))
				.then((response) => {
					this.response = response
				})
			.catch(exception => $wnl.logger.capture(exception))
		},
	}
</script>
