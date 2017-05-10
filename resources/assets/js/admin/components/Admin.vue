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
	import store from 'store'
	import {mapActions} from 'vuex'
	import {swalConfig} from 'js/utils/swal'

	export default {
		name: 'Admin',
		components: {
			'wnl-navbar': Navbar
		},
		methods: {
			...mapActions(['setupCurrentUser']),
		},
		created: function () {
			this.setupCurrentUser()
		}
	}
</script>
