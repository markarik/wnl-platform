<template>
	<div class="wnl-app-layout">
		<div class="wnl-app-layout-left">
			<aside class="wnl-sidenav wnl-left-content">
				<ul class="wnl-sidenav-items">
					<li class="wnl-sidenav-item">
						<router-link :to="{name: 'my-orders'}">Twoje zamówienia</router-link>
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
	export default {
		props: ['view'],
		methods: {
			goToDefaultRoute() {
				if (!this.view) {
					this.$router.replace({ name: 'my-orders' })
				}
			}
		},
		mounted() { this.goToDefaultRoute() }
	}
</script>
