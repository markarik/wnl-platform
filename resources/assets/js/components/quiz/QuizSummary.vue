<template>
	<div class="wnl-quiz-summary">
		<wnl-quiz-stats></wnl-quiz-stats>
		<wnl-quiz-list
			:allQuestions="getQuestionsWithAnswersAndStats"
			:getReaction="getReaction"
			:isProcessing="isProcessing"
			:isComplete="isComplete"
			module="quiz"
			@resetState="resetState"
		/>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

	.wnl-quiz-summary
		margin-top: $margin-big
</style>

<script>
	import QuizList from 'js/components/quiz/QuizList.vue'
	import QuizStats from 'js/components/quiz/QuizStats.vue'
	import { mapGetters, mapActions } from 'vuex'

	export default {
		name: 'QuizSummary',
		components: {
			'wnl-quiz-list': QuizList,
			'wnl-quiz-stats': QuizStats,
		},
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
	}
</script>
