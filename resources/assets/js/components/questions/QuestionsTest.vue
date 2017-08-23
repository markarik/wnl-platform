<template>
	<div class="wnl-app-layout">
		<div v-if="!results">
			<wnl-quiz-timer :time="time" @timesUp="checkQuiz" ref="timer"/>
			<wnl-quiz-list
				:allQuestions="questions"
				:isComplete="false"
				:isProcessing="false"
				@selectAnswer="onSelectAnswer"
				@checkQuiz="checkQuiz"
			/>
		</div>
		<div v-else>
			TEST ROZWIAZANY!!!!
			<button @click="$emit('endTest')">Wroć do bazy pytań</button>
			<wnl-quiz-list
				:allQuestions="questions"
				:isComplete="true"
				:readOnly="true"
				:isProcessing="false"
				:getReaction="getReaction"
				module="questions"
			/>
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
	props: ['questions', 'time', 'onSelectAnswer', 'onCheckQuiz', 'getReaction'],
	components: {
		'wnl-quiz-list': QuizList,
		'wnl-quiz-timer': QuizTimer
	},
	data() {
		return {
			results: false
		}
	},
	methods: {
		checkQuiz() {
			this.$refs.timer.stopTimer()
			this.onCheckQuiz().then((results) => this.results = results)
		}
	},
	mounted() {
		!this.results && this.$refs.timer.startTimer()
	}
}
</script>
