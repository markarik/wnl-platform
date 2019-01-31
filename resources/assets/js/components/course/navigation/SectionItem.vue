<template>
	<wnl-lesson-navigation-item :navigationItem="sectionItem" class="margin left">
		<wnl-subsection-item
			v-for="subsection in sectionSubsections"
			:key="subsection.id"
			:item="subsection"
			slot="children"
		></wnl-subsection-item>
	</wnl-lesson-navigation-item>
</template>

<script>
import {mapGetters} from 'vuex';
import navigation from 'js/services/navigation';
import WnlSubsectionItem from 'js/components/course/navigation/SubsectionItem';
import WnlLessonNavigationItem from 'js/components/course/navigation/LessonNavigationItem';

export default {
	name: 'SectionItem',
	components: {
		WnlSubsectionItem,
		WnlLessonNavigationItem
	},
	props: {
		item: {
			type: Object,
			required: true
		},
	},
	computed: {
		...mapGetters(['lessonState']),
		...mapGetters('course', ['getSubsectionsForSection']),
		...mapGetters('progress', {
			getSectionProgress: 'getSection'
		}),
		lessonId() {
			return this.$route.params.lessonId;
		},
		courseId() {
			return this.$route.params.courseId;
		},
		screenId() {
			return this.item.screens;
		},
		sectionItem() {
			const section = this.item;
			const params = {
				courseId: this.courseId,
				lessonId: this.lessonId,
				screenId: this.screenId,
				slide: section.slide,
			};
			const isSectionActive = this.lessonState.activeSection === section.id;
			return navigation.composeItem({
				text: section.name,
				itemClass: 'small subitem todo',
				routeName: 'screens',
				routeParams: params,
				method: 'replace',
				iconClass: 'fa-angle-right',
				iconTitle: section.name,
				completed: this.getSectionProgress(this.courseId, this.lessonId, this.screenId, section.id),
				active: isSectionActive,
				meta: `(${section.slidesCount})`
			});
		},
		sectionSubsections() {
			return this.getSubsectionsForSection(this.item.id);
		}
	},
};
</script>
