<template>
	<div class="wnl-app-layout">
		<wnl-quiz-timer :time="time" @timesUp="checkQuiz" ref="timer"/>
		<wnl-quiz-list v-if="!results"
			:allQuestions="questions"
			:isComplete="false"
			:isProcessing="false"
			@selectAnswer="onSelectAnswer"
			@checkQuiz="checkQuiz"
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
import QuizTimer from 'js/components/quiz/QuizTimer'
import Vue from 'vue'

export default {
	props: ['questions', 'time', 'onSelectAnswer', 'onCheckQuiz', 'results'],
	components: {
		'wnl-quiz-list': QuizList,
		'wnl-quiz-timer': QuizTimer
	},
	methods: {
		checkQuiz() {
			this.$refs.timer.stopTimer()
			this.onCheckQuiz()
		}
	},
	mounted() {
		!this.results && this.$refs.timer.startTimer()
	}
}
</script>
