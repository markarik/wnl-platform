<template>
	<div id="app">
		<wnl-navbar :show="true"></wnl-navbar>
		<div class="admin-main">
			<div class="admin-left">
				<wnl-sidenav></wnl-sidenav>
			</div>
			<div class="admin-right">
				<router-view></router-view>
			</div>
		</div>
	</div>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'

	.admin-main
		align-items: stretch
		display: flex
		height: 100%

	.admin-left
		border-right: $border-light-gray
		flex: 1 auto
		max-width: 300px
		padding: $margin-base

	.admin-right
		flex: 8 auto
		padding: $margin-base
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
