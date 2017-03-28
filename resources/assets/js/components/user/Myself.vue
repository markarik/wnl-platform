<template>
	<div class="wnl-app-layout">
		<div class="wnl-app-layout-left">
			<aside class="wnl-sidenav wnl-left-content">
				<ul class="wnl-sidenav-items">
					<li class="wnl-sidenav-item">
						<router-link :to="{name: 'my-orders'}">Twoje zamówienia</router-link>
					</li>
					<li class="wnl-sidenav-item" v-if="isProduction">
						<router-link :to="{name: 'countdown'}">Kiedy kurs?</router-link>
					</li>
				</ul>
			</aside>
		</div>
		<div class="wnl-app-layout-main">
			<router-view></router-view>
		</div>
		<div class="wnl-app-layout-right"></div>
	</div>
</template>

<script>
	import { isProduction } from 'js/utils/env'

	export default {
		props: ['view'],
		computed: {
			isProduction() {
				return isProduction()
			},
		},
		methods: {
			goToDefaultRoute() {
				if (!this.view) {
					this.$router.replace({ name: 'my-orders' })
				}
			}
		},
		// mounted() { this.goToDefaultRoute() }
	}
</script>
