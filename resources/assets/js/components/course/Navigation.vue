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
import {resource} from 'js/utils/config';
import navigation from 'js/services/navigation';

export default {
	name: 'Navigation',
	components: {
		WnlCourseNavigation, WnlLessonNavigation
	},
	props: ['context', 'isLesson'],
	computed: {
		...mapGetters(['currentUserRoles', 'lessonState']),
		...mapGetters('course', [
			'courseId',
		]),
		...mapGetters('progress', {
			getScreenProgress: 'getScreen',
			getSectionProgress: 'getSection'
		}),
	},
	methods: {
		getSectionItem(section) {
			const params = {
				courseId: this.courseId,
				lessonId: section[resource('lessons')],
				screenId: section[resource('screens')],
				slide: section.slide,
			};

			const screen = this.getScreenProgress(params.courseId, params.lessonId, params.screenId);
			const sections = screen && screen.sections || {};
			const isSectionActive = this.lessonState.activeSection === section.id;

			return navigation.composeItem({
				text: section.name,
				itemClass: 'small subitem todo',
				routeName: resource('screens'),
				routeParams: params,
				method: 'replace',
				iconClass: 'fa-angle-right',
				iconTitle: section.name,
				completed: !!sections[section.id],
				active: isSectionActive,
				meta: `(${section.slidesCount})`
			});
		},
		getSubsectionItem(subsection, section) {
			const params = {
				courseId: this.courseId,
				lessonId: subsection[resource('lessons')],
				screenId: subsection[resource('screens')],
				slide: subsection.slide,
			};

			const sectionProgress = this.getSectionProgress(params.courseId, params.lessonId, params.screenId, section.id) || {};
			const completedSubsections = sectionProgress.subsections || {};
			const isSubsectionActive = this.lessonState.activeSubsection === subsection.id;

			return navigation.composeItem({
				text: subsection.name,
				itemClass: 'small subitem--second todo',
				routeName: resource('screens'),
				routeParams: params,
				method: 'replace',
				iconClass: 'fa-angle-right',
				iconTitle: subsection.name,
				completed: !!completedSubsections[subsection.id],
				active: isSubsectionActive,
				meta: `(${subsection.slidesCount})`
			});
		}
	}
};
</script>
