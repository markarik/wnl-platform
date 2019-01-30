<template>
	<aside class="sidenav-aside course-sidenav">
		<wnl-lesson-navigation v-if="isLesson"></wnl-lesson-navigation>
		<wnl-course-navigation v-else></wnl-course-navigation>
	</aside>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

	.course-sidenav
		flex: 1
		min-width: $sidenav-min-width
		overflow: auto
		padding: 7px 0
		width: $sidenav-width
</style>

<script>
import {mapGetters} from 'vuex';

import WnlCourseNavigation from 'js/components/course/navigation/CourseNavigation';
import WnlLessonNavigation from 'js/components/course/navigation/LessonNavigation';

export default {
	name: 'Navigation',
	components: {
		WnlCourseNavigation, WnlLessonNavigation
	},
	props: ['context', 'isLesson'],
	computed: {
		...mapGetters(['currentUserRoles', 'lessonState']),
		...mapGetters('progress', {
			getScreenProgress: 'getScreen',
			getSectionProgress: 'getSection'
		}),
	},
};
</script>
