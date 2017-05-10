<template>
	<div id="app">
		<wnl-navbar :show="true"></wnl-navbar>
	</div>
</template>

<style lang="sass">

</style>

<script>
	import Navbar from 'js/components/global/Navbar.vue'
	import { mapActions } from 'vuex'

	export default {
		name: 'Admin',
		components: {
			'wnl-navbar': Navbar,
		},
		methods: {
			...mapActions(['setupCurrentUser']),
		},
		mounted() {
			this.setupCurrentUser()
		},
	}
</script>
