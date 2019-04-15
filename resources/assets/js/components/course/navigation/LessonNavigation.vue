<template>
	<div>
		<span class="item-wrapper heading">
			{{lesson.name}}
		</span>
		<wnl-screen-item
			v-for="screen in screens"
			:key="screen.id"
			:item="screen"
			:lesson-id="lessonId"
		></wnl-screen-item>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.item-wrapper
		display: flex
		line-height: $line-height-base
		padding: $margin-base
		word-break: break-word
		word-wrap: break-word
		font-size: $font-size-minus-1

</style>

<script>
import { mapGetters } from 'vuex';
import WnlScreenItem from 'js/components/course/navigation/ScreenItem';

export default {
	name: 'LessonNavigation',
	components: {
		WnlScreenItem
	},
	computed: {
		...mapGetters('course', [
			'getScreensForLesson',
			'getLesson'
		]),
		lessonId() {
			return this.$route.params.lessonId;
		},
		lesson() {
			return this.getLesson(this.lessonId) || {};
		},
		screens() {
			return this.getScreensForLesson(this.lessonId);
		},
	},
};
</script>
