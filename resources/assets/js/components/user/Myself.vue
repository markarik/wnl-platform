<template>
	<div class="wnl-app-layout">
		<div class="wnl-left wnl-app-layout-left">
			<aside class="wnl-sidenav wnl-left-content">
				<wnl-sidenav :items="items"></wnl-sidenav>
			</aside>
		</div>
		<div class="wnl-middle wnl-app-layout-main">
			<router-view></router-view>
		</div>
		<div class="wnl-right wnl-app-layout-right"></div>
	</div>
</template>

<script>
	import Sidenav from 'js/components/global/Sidenav.vue'
	import { isProduction } from 'js/utils/env'

	export default {
		props: ['view'],
		computed: {
			isProduction() {
				return isProduction()
			},
			items() {
				let items = [
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
		},
		// mounted() { this.goToDefaultRoute() }
	}
</script>
