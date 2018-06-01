<template>
	<div class="wnl-app-layout">
		<wnl-sidenav-slot
			:isVisible="isSidenavVisible"
			:isDetached="!isSidenavMounted"
		>
			<wnl-main-nav :isHorizontal="!isSidenavMounted"></wnl-main-nav>
			<aside class="sidenav-aside myself-sidenav">
				<wnl-sidenav :items="items"></wnl-sidenav>
			</aside>
		</wnl-sidenav-slot>
		<div class="wnl-middle wnl-app-layout-main" :class="{'full-width': isMobileProfile, 'mobile-main': isMobileProfile}">
			<router-view v-if="!isMainRoute"></router-view>
			<wnl-my-profile v-else></wnl-my-profile>
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
	import MyProfile from 'js/components/user/MyProfile'
	import Sidenav from 'js/components/global/Sidenav'
	import SidenavSlot from 'js/components/global/SidenavSlot'
	import { isProduction } from 'js/utils/env'

	export default {
		name: 'Myself',
		components: {
			'wnl-main-nav': MainNav,
			'wnl-my-profile': MyProfile,
			'wnl-sidenav': Sidenav,
			'wnl-sidenav-slot': SidenavSlot,
		},
		props: ['view'],
		computed: {
			...mapGetters(['isSidenavMounted', 'isSidenavVisible', 'isMobileProfile']),
			isProduction() {
				return isProduction()
			},
			items() {
				let items = [
					{
						text: 'Konto',
						itemClass: 'heading small',
					},
					{
						text: 'Twoje zamówienia',
						itemClass: 'has-icon',
						to: {
							name: 'my-orders',
							params: {},
						},
						isDisabled: false,
						method: 'push',
						iconClass: 'fa-shopping-cart',
						iconTitle: 'Twoje zamówienia',
					},
					{
						text: 'Profil publiczny',
						itemClass: 'has-icon',
						to: {
							name: 'my-profile',
							params: {},
						},
						isDisabled: false,
						method: 'push',
						iconClass: 'fa-user',
						iconTitle: 'Profil publiczny',
					},
					{
						text: 'Adres',
						itemClass: 'has-icon',
						to: {
							name: 'my-address',
							params: {},
						},
						isDisabled: false,
						method: 'push',
						iconClass: 'fa-address-card-o',
						iconTitle: 'Profil publiczny',
					},
					{
						text: 'Dane do faktury',
						itemClass: 'has-icon',
						to: {
							name: 'my-billing-data',
							params: {},
						},
						isDisabled: false,
						method: 'push',
						iconClass: 'fa-file-o',
						iconTitle: 'Profil publiczny',
					},
					{
						text: 'Ustawienia',
						itemClass: 'has-icon',
						to: {
							name: 'my-settings',
							params: {},
						},
						isDisabled: false,
						method: 'push',
						iconClass: 'fa-sliders',
						iconTitle: 'Profil publiczny',
					},
					{
						text: 'Statystyki',
						itemClass: 'has-icon',
						to: {
							name: 'stats'
						},
						isDisabled: false,
						method: 'push',
						iconClass: 'fa-line-chart',
						iconTitle: 'Statystyki',
					},
					{
						text: 'Plan pracy',
						itemClass: 'has-icon',
						to: {
							name: 'lessons-availabilites'
						},
						isDisabled: false,
						method: 'push',
						iconClass: 'fa fa-tasks',
						iconTitle: 'Plan pracy',
					},
					{
						text: 'Usuwanie postępu',
						itemClass: 'has-icon',
						to: {
							name: 'progress-reset'
						},
						isDisabled: false,
						method: 'push',
						iconClass: 'fa fa-exclamation-triangle',
						iconTitle: 'Usuwanie postępu',
					}
					// {
					// 	text: 'Zmiana hasła',
					// 	itemClass: 'has-icon',
					// 	to: {
					// 		name: 'my-password',
					// 		params: {},
					// 	},
					// 	isDisabled: false,
					// 	method: 'push',
					// 	iconClass: 'fa-key',
					// 	iconTitle: 'Profil publiczny',
					// },
				]

				return items
			},
			isMainRoute() {
				return this.$route.name === 'myself'
			},
		},
		methods: {
			...mapActions(['killChat']),
			goToDefaultRoute() {
				if (!this.view) {
					this.$router.replace({ name: 'my-orders' })
				}
			}
		},
	}
</script>
