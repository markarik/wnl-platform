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
		...mapGetters('course', [
			'getScreens'
		]),
		screens() {
			return this.getScreens(this.$route.params.lessonId);
		},
	},
};
</script>
