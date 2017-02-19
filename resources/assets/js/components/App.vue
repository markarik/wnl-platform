<template>
	<div id="app">
		<wnl-navbar :show="true"></wnl-navbar>
		<router-view></router-view>
	</div>
</template>

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
