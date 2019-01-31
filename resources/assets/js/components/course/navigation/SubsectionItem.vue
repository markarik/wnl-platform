<template>
	<wnl-lesson-navigation-item :navigationItem="subsectionItem" class="margin left"></wnl-lesson-navigation-item>
</template>

<script>
import {mapGetters} from 'vuex';
import navigation from 'js/services/navigation';
import {STATUS_COMPLETE} from 'js/services/progressStore';
import WnlLessonNavigationItem from 'js/components/course/navigation/LessonNavigationItem';

export default {
	name: 'SubsectionItem',
	components: { WnlLessonNavigationItem },
	props: {
		item: {
			type: Object,
			required: true
		},
	},
	computed: {
		...mapGetters(['lessonState']),
		...mapGetters('progress', {
			getSectionProgress: 'getSection'
		}),
		lessonId() {
			return this.item.lessons;
		},
		courseId() {
			return this.$route.params.courseId;
		},
		sectionId() {
			return this.item.sections;
		},
		screenId() {
			return this.item.screens;
		},
		subsectionItem() {
			const subsection = this.item;
			const params = {
				courseId: this.courseId,
				lessonId: this.lessonId,
				screenId: this.screenId,
				slide: subsection.slide,
			};
			const isSubsectionActive = this.lessonState.activeSubsection === subsection.id;
			const sectionProgress = this.getSectionProgress(
				this.courseId, this.lessonId, this.screenId, this.sectionId
			);
			const subsectionsProgress = sectionProgress && sectionProgress.subsections;
			const subsectionProgress = subsectionsProgress && subsectionsProgress[subsection.id];
			return navigation.composeItem({
				text: subsection.name,
				itemClass: 'small subitem todo',
				routeName: 'screens',
				routeParams: params,
				method: 'replace',
				iconClass: 'fa-angle-right',
				iconTitle: subsection.name,
				completed: subsectionProgress && subsectionProgress.status === STATUS_COMPLETE,
				active: isSubsectionActive,
				meta: `(${subsection.slidesCount})`
			});
		},
	},
};
</script>
