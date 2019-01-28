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
import {STATUS_COMPLETE} from '../../services/progressStore';
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
			'name',
			'groups',
			'structure',
			'getLesson',
			'getScreens',
			'isLessonAvailable',
			'courseId',
		]),
		...mapGetters('progress', {
			getCourseProgress: 'getCourse',
			getScreenProgress: 'getScreen',
			getLessonProgress: 'getLesson',
			getSectionProgress: 'getSection'
		}),
		sidenavOptions() {
			return {
				hasGroups: !this.isLesson,
				showSubitemsCount: true,
			};
		},
		isStructureEmpty() {
			return typeof this.structure !== 'object' || this.structure.length === 0;
		},
		courseProgress() {
			return this.getCourseProgress(this.context.courseId);
		},
		itemsHeading() {
			return this.isLesson ? this.getLesson(this.context.lessonId).name : null;
		},
		items() {
			if (this.isLesson) {
				return this.getLessonNavigation();
			} else {
				return this.getCourseNavigation();
			}
		},
	},
	methods: {
		getScreenItem(screen) {
			const params = {
				courseId: this.courseId,
				lessonId: screen[resource('lessons')],
				screenId: screen.id,
			};

			const lesson = this.getLessonProgress(params.courseId, params.lessonId);
			const screens = lesson && lesson.screens || [];
			const completed = screens[screen.id] && screens[screen.id].status === STATUS_COMPLETE;
			const itemProps = {
				text: screen.name,
				itemClass: 'todo',
				routeName: resource('screens'),
				routeParams: params,
				completed
			};

			if (screen.slides_count) {
				return navigation.composeItem({...itemProps, meta: `(${screen.slides_count})`});
			}

			return navigation.composeItem(itemProps);
		},
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
