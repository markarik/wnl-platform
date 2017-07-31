<template>
	<div class="wnl-quiz-summary">
		<wnl-quiz-stats></wnl-quiz-stats>
		<wnl-quiz-list></wnl-quiz-list>
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
	import { mapGetters } from 'vuex'

	export default {
		name: 'QuizSummary',
		components: {
			'wnl-quiz-list': QuizList,
			'wnl-quiz-stats': QuizStats,
		},
	}
</script>
