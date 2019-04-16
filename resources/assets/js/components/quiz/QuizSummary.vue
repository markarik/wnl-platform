<template>
	<div class="wnl-quiz-summary">
		<wnl-quiz-stats />
		<wnl-quiz-list
			:all-questions="getQuestionsWithAnswersAndStats"
			:get-reaction="getReaction"
			:is-processing="isProcessing"
			:is-complete="isComplete"
			module="quiz"
			@resetState="resetState"
			@userEvent="proxyUserEvent"
		/>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

	.wnl-quiz-summary
		margin-top: $margin-big
</style>

<script>
import QuizList from 'js/components/quiz/QuizList.vue';
import QuizStats from 'js/components/quiz/QuizStats.vue';
import emits_events from 'js/mixins/emits-events';
import { mapGetters, mapActions } from 'vuex';

export default {
	name: 'QuizSummary',
	components: {
		'wnl-quiz-list': QuizList,
		'wnl-quiz-stats': QuizStats,
	},
	mixins: [emits_events],
	computed: {
		...mapGetters('quiz', [
			'isComplete',
			'getQuestionsWithAnswersAndStats',
			'getReaction',
			'isProcessing',
		]),
	},
	methods: {
		...mapActions('quiz', [
			'resetState',
			'checkQuiz'
		]),
	},
};
</script>
