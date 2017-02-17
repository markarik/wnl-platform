<template>
	<div id="app">
		<wnl-navbar></wnl-navbar>
		<component :is="currentView"></component>
	</div>
</template>

<script>
	// Import global components
	import Navbar from './global/Navbar.vue'

	// Import main views components
	import Course from './Course.vue'
	import Lesson from './Lesson.vue'

	export default {
		name: 'App',
		components: {
			'wnl-navbar': Navbar,
			'wnl-course': Course,
			'wnl-lesson': Lesson
		},
		computed: {
			currentView () {
				return this.$store.getters.currentView
			}
		}
	}
</script>
