<template>
	<div id="app">
		<!-- <wnl-navbar :show="true"></wnl-navbar> -->
		<div class="admin-main">
			<div class="admin-left">
				<wnl-sidenav></wnl-sidenav>
			</div>
			<div class="admin-right">
				<img src="http://stowarzyszeniemotyl.org/Administracja/Gfx/design/main_logo.png"/>
				<router-view></router-view>
			</div>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.admin-main
		align-items: stretch
		display: flex
		height: 100%

	.admin-left
		border-right: $border-light-gray
		flex: 0 auto
		min-width: 250px
		padding: $margin-big

	.admin-right
		flex: 8 auto
		height: 100%
		max-height: 100%
		overflow: auto
		padding: $margin-big
		position: relative
</style>

<script>
	import Navbar from 'js/components/global/Navbar.vue'
	import Sidenav from 'js/admin/components/Sidenav.vue'
	import { mapActions } from 'vuex'

	export default {
		name: 'Admin',
		components: {
			'wnl-navbar': Navbar,
			'wnl-sidenav': Sidenav,
		},
		methods: {
			...mapActions(['setupCurrentUser']),
		},
		mounted() {
			this.setupCurrentUser()
		},
	}
</script>
