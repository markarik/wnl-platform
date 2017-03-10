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
	import Navbar from './global/Navbar.vue'
	import { mapActions } from 'vuex'

	export default {
		name: 'App',
		components: {
			'wnl-navbar': Navbar
		},
		methods: {
			...mapActions(['setCurrentUser'])
		},
		created: function () {
			this.setCurrentUser()
			axios.put('papi/v1/editions/1/user_progress/1', {
				lesson_id: 1,
				status: 'done',
				route: {courses: 1,lessons: 1,screens: 1}
			})
		}
	}
</script>
