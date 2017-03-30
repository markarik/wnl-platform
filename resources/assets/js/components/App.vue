<template>
	<div id="app">
		<wnl-navbar :show="true"></wnl-navbar>
		<div class="wnl-main">
			<router-view></router-view>
		</div>
	</div>
</template>

<script>
	// Import global components
	import Navbar from 'js/components/global/Navbar.vue'
	import { mapActions } from 'vuex'

	export default {
		name: 'App',
		components: {
			'wnl-navbar': Navbar
		},
		methods: {
			...mapActions(['setupCurrentUser'])
		},
		created: function () {
			this.setupCurrentUser()
		}
	}
</script>
