<template>
	<div class="wnl-app-layout">
		{{ time }}
		<wnl-quiz-list v-if="!results"
			:allQuestions="questions"
			:isComplete="false"
			:isProcessing="false"
			@selectAnswer="onSelectAnswer"
			@checkQuiz="onCheckQuiz"
		/>
		<div v-else>
			TEST ROZWIAZANY!!!!
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
</style>

<script>
import QuizList from 'js/components/quiz/QuizList'
import Vue from 'vue'

export default {
	props: ['questions', 'time', 'onSelectAnswer', 'onCheckQuiz', 'results'],
	components: {
		'wnl-quiz-list': QuizList
	}
}
</script>
