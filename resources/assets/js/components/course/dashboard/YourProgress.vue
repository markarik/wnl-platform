<template>
	<div class="your-progress">
		<p class="heading">{{$t('dashboard.progress.howYouDoin')}}</p>
		<wnl-progress
			:value="progressValue"
			:max="progressMax"
			:has-numbers="progressHasNumbers"
			:modifying-class="progressModifyingClass"
		>
		</wnl-progress>
		<p class="progress-message">{{progressMessage}}</p>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.heading
		text-align: center

	.progress-message
		color: $color-darker-gray
		font-size: $font-size-minus-1
		text-align: center
</style>

<script>
import emoji from 'node-emoji';
import Progress from 'js/components/global/Progress.vue';
import { mapGetters } from 'vuex';

const STATE_FULL = 'full',
	STATE_GOOD = 'good',
	STATE_WARNING = 'warning',
	STATE_DANGER = 'danger',
	stateData = {
		[STATE_FULL]: {
			message: `Świetnie Ci idzie! Wszystkie dostępne lekcje są już zakończone i należy Ci się zasłużony odpoczynek! ${emoji.get('slightly_smiling_face')}`,
			modifyingClass: 'is-success'
		},
		[STATE_GOOD]: {
			message: 'Jesteś na dobrej drodze! Tak trzymaj, a spokojnie się wyrobisz. :)',
			modifyingClass: 'is-success'
		},
		[STATE_WARNING]: {
			message: 'Jesteś na dobrej drodze! Tak trzymaj, ucz się spokojnie, ale postaraj się trochę nadrobić materiał. :)',
			modifyingClass: 'is-warning'
		},
		[STATE_DANGER]: {
			message: 'Postaraj się nadrobić zaległości lub nie dopuść do ich powiększenia. :)',
			modifyingClass: 'is-danger'
		},
	};

export default {
	computed: {
		...mapGetters('course', [
			'userLessons',
		]),
		...mapGetters('progress', [
			'getCompleteLessons'
		]),
		progressLessons() {
			return this.userLessons.filter(lesson => lesson.isAvailable && lesson.is_required);
		},
		courseId() {
			return this.$route.params.courseId;
		},
		progressValue() {
			return this.getCompleteLessons(this.courseId).length;
		},
		progressMax() {
			return this.progressLessons.length;
		},
		progressState() {
			const incompleteLessons = this.progressMax - this.progressValue;

			if (incompleteLessons === 0) {
				return 'full';
			} else if (incompleteLessons < 7) {
				return 'good';
			} else if (incompleteLessons < 14) {
				return 'warning';
			}
			return 'danger';
		},
		progressHasNumbers() {
			return true;
		},
		progressModifyingClass() {
			return stateData[this.progressState].modifyingClass;
		},
		progressMessage() {
			return stateData[this.progressState].message;
		},
	},
	components: {
		'wnl-progress': Progress,
	},
};
</script>
