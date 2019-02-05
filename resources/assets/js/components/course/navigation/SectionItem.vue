<template>
	<wnl-lesson-navigation-item
		:to="to"
		:is-completed="isCompleted"
		:is-active="isActive"
		:meta="meta"
		class="margin left small"
	>
		<span slot="title">{{item.name}}</span>
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
import {STATUS_COMPLETE} from 'js/services/progressStore';
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
		to() {
			const params = {
				courseId: this.courseId,
				lessonId: this.lessonId,
				screenId: this.screenId,
				slide: this.item.slide,
			};

			return {
				name: 'screens',
				params
			};
		},
		isCompleted() {
			const sectionProgress = this.getSectionProgress(this.courseId, this.lessonId, this.screenId, this.item.id);
			return sectionProgress && sectionProgress.status === STATUS_COMPLETE;
		},
		isActive() {
			return this.lessonState.activeSection === this.item.id;
		},
		meta() {
			return `(${this.item.slidesCount})`;
		},
		sectionSubsections() {
			return this.getSubsectionsForSection(this.item.id);
		}
	},
};
</script>
