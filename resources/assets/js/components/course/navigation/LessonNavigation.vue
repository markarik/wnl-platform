<template>
		<div>
			<span class="item-wrapper heading">
				{{lesson.name}}
			</span>
			<wnl-screen-item v-for="screen in screens" :key="screen.id" :item="screen" :lessonId="lessonId"></wnl-screen-item>
		</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.item-wrapper
		display: flex
		line-height: 1.5em
		padding: 14px 15px
		word-break: break-word
		word-wrap: break-word
		font-size: .875rem

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
		...mapGetters('course', [
			'getScreens',
			'getLesson'
		]),
		lessonId() {
			return this.$route.params.lessonId;
		},
		lesson() {
			return this.getLesson(this.lessonId) || {};
		},
		screens() {
			return this.getScreens(this.lessonId);
		},
	},
};
</script>
