<template>
	<div class="wnl-app-layout">
		<wnl-sidenav-slot
			:isVisible="isSidenavVisible"
			:isDetached="!isSidenavMounted"
		>
			<wnl-main-nav :isHorizontal="!isSidenavMounted"></wnl-main-nav>
			<aside class="wnl-sidenav">
				<wnl-sidenav :items="items" :breadcrumbs="breadcrumbs"></wnl-sidenav>
			</aside>
		</wnl-sidenav-slot>
		<div class="wnl-middle wnl-app-layout-main" v-bind:class="{'full-width': isMobileProfile}">
			<router-view></router-view>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-sidenav
		padding: $margin-small
</style>

<script>
	import Sidenav from 'js/components/global/Sidenav.vue'
	import { isProduction } from 'js/utils/env'
	import { mapGetters } from 'vuex'
	import SidenavSlot from 'js/components/global/SidenavSlot'
	import MainNav from 'js/components/MainNav'

	export default {
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

				if (this.isProduction) {
					items.push({
						text: 'Kiedy kurs?',
						itemClass: 'has-icon',
						to: {
							name: 'countdown',
							params: {},
						},
						isDisabled: false,
						method: 'push',
						iconClass: 'fa-question',
						iconTitle: 'Kiedy kurs?',
					})
				}

				return items
			},
			breadcrumbs() {
				let breadcrumbs = [
					{
						text: 'Plan lekcji',
						itemClass: 'has-icon',
						to: {
							name: 'dashboard',
							params: {},
						},
						isDisabled: false,
						method: 'push',
						iconClass: 'fa-home',
						iconTitle: 'Plan lekcji',
					},
				]

				return breadcrumbs
			}
		},
		methods: {
			goToDefaultRoute() {
				if (!this.view) {
					this.$router.replace({ name: 'my-orders' })
				}
			}
		},
		components: {
			'wnl-sidenav': Sidenav,
			'wnl-sidenav-slot': SidenavSlot,
			'wnl-main-nav': MainNav
		},
		// mounted() { this.goToDefaultRoute() }
	}
</script>
