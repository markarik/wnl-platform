<template>
	<wnl-lesson-navigation-item
		:to="to"
		:is-completed="isCompleted"
		:is-active="isActive"
		:is-disabled="isDisabled"
		:meta="meta"
		class="margin left small"
	>
		<span slot="title">{{item.name}}</span>
	</wnl-lesson-navigation-item>
</template>

<script>
import {mapGetters} from 'vuex';
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
		...mapGetters('course', ['isLessonAvailable']),
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
			const sectionProgress = this.getSectionProgress(
				this.courseId, this.lessonId, this.screenId, this.sectionId
			);
			const subsectionsProgress = sectionProgress && sectionProgress.subsections;
			const subsectionProgress = subsectionsProgress && subsectionsProgress[this.item.id];

			return subsectionProgress && subsectionProgress.status === STATUS_COMPLETE;
		},
		isActive() {
			return this.lessonState.activeSubsection === this.item.id;
		},
		isDisabled() {
			return !this.isLessonAvailable(this.lessonId);
		},
		meta() {
			return `(${this.item.slidesCount})`;
		}
	},
};
</script>
