<template>
	<wnl-lesson-navigation-item
		:to="to"
		:is-completed="isCompleted"
		:is-disabled="isDisabled"
		:meta="meta"
	>
		<span slot="title">{{item.name}}</span>
		<wnl-section-item
			v-for="section in screenSections"
			:key="section.id"
			slot="children"
			:item="section"
		/>
	</wnl-lesson-navigation-item>
</template>

<script>
import { mapGetters } from 'vuex';
import { STATUS_COMPLETE } from 'js/services/progressStore';
import WnlSectionItem from 'js/components/course/navigation/SectionItem';
import WnlLessonNavigationItem from 'js/components/course/navigation/LessonNavigationItem';

export default {
	name: 'ScreenItem',
	components: {
		WnlSectionItem,
		WnlLessonNavigationItem
	},
	props: {
		item: {
			type: Object,
			required: true
		},
	},
	computed: {
		...mapGetters('course', ['getSectionsForScreen', 'isLessonAvailable']),
		...mapGetters('progress', {
			getScreenProgress: 'getScreen',
		}),
		lessonId() {
			return this.item.lessons;
		},
		courseId() {
			return this.$route.params.courseId;
		},
		screenId() {
			return this.item.id;
		},
		to() {
			const params = {
				courseId: this.courseId,
				lessonId: this.lessonId,
				screenId: this.screenId,
				slide: this.item.slide,
			};

			return {
				name: 'lessons',
				params
			};
		},
		isCompleted() {
			const screenProgress = this.getScreenProgress(this.courseId, this.lessonId, this.screenId) || {};

			return screenProgress.status === STATUS_COMPLETE;
		},
		isDisabled() {
			return !this.isLessonAvailable(this.lessonId);
		},
		meta() {
			return this.item.slides_count && `(${this.item.slides_count})`;
		},
		screenSections() {
			return this.getSectionsForScreen(this.screenId);
		}
	},
};
</script>
