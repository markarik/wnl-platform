<template>
	<div id="app">
		<wnl-navbar :show="true"></wnl-navbar>
		<div class="wnl-main">
			<router-view></router-view>
		</div>
	</div>
</template>

<style lang="sass">
	.wnl-main
		padding: 20px 0
</style>

<script>
	// Import global components
	import Navbar from './global/Navbar.vue'
	import { mapActions } from 'vuex'

	export default {
		name: 'App',
		components: { 'wnl-navbar': Navbar },
		methods: {
			...mapActions(['setCurrentUser'])
		},
		created: function () {
			this.setCurrentUser()
		}
	}
</script>
