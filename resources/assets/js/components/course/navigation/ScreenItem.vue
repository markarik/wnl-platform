<template>
	<wnl-lesson-navigation-item :navigationItem="screenItem">
		<wnl-section-item v-for="section in screenSections" :key="section.id" :item="section" slot="children"/>
	</wnl-lesson-navigation-item>
</template>

<script>
import {mapGetters} from 'vuex';
import navigation from 'js/services/navigation';
import {STATUS_COMPLETE} from 'js/services/progressStore';
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
		...mapGetters('course', ['getSectionsForScreen']),
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
		screenItem() {
			const screen = this.item;
			const params = {
				courseId: this.courseId,
				lessonId: this.lessonId,
				screenId: this.screenId,
			};
			const screenProgress = this.getScreenProgress(this.courseId, params.lessonId, screen.id) || {};
			const itemProps = {
				text: screen.name,
				itemClass: 'todo',
				routeName: 'screens',
				routeParams: params,
				completed: screenProgress.status === STATUS_COMPLETE
			};

			return navigation.composeItem({...itemProps, ...screen.slides_count && {meta: `(${screen.slides_count})`}});
		},
		screenSections() {
			return this.getSectionsForScreen(this.screenId);
		}
	},
};
</script>
