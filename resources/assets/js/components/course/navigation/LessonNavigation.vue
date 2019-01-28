<template>
		<div>
			<wnl-screen-item v-for="screen in screens" :key="screen.id" :item="screen"></wnl-screen-item>
		</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
</style>

<script>
import {mapGetters} from 'vuex';
import navigation from 'js/services/navigation';
import WnlScreenItem from 'js/components/course/navigation/ScreenItem';

export default {
	name: 'LessonNavigation',
	components: {
		WnlScreenItem
	},
	computed: {
		...mapGetters('progress', {
			getCourseProgress: 'getCourse',
		}),
		...mapGetters('course', [
			'isLessonAvailable',
			'getScreens'
		]),
		screens() {
			return this.getScreens(this.$route.params.lessonId);
		},
		courseProgress() {
			return this.getCourseProgress(1);
		},
		isInProgress() {
			return this.hasClass('in-progress');
		},
		isComplete() {
			return this.hasClass('complete');
		},
		itemClass() {
			return this.lessonItem.itemClass;
		},
		to() {
			return this.lessonItem.to;
		},
		lessonItem() {
			const lesson = this.item.model;
			let cssClass = 'is-grouped with-progress', iconClass = '', iconTitle = '';

			if (this.courseProgress.lessons && this.courseProgress.lessons.hasOwnProperty(lesson.id)) {
				cssClass = `${cssClass} ${this.courseProgress.lessons[lesson.id].status}`;
			}

			return navigation.composeItem({
				text: lesson.name,
				itemClass: cssClass,
				routeName: 'lessons',
				routeParams: {
					courseId: 1,
					lessonId: lesson.id,
				},
				isDisabled: !this.isLessonAvailable(lesson.id),
				iconClass,
				iconTitle
			});
		}
	},
	methods: {
		hasClass(className) {
			return !!this.itemClass && this.itemClass.indexOf(className) > -1;
		}
	},
};
</script>
