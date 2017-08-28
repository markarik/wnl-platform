<template>
	<div>
		<div v-if="!isComplete">
			<wnl-quiz-timer :time="time" @timesUp="$emit('checkQuiz')" ref="timer"/>
		</div>
		<div v-else>
			TEST ROZWIAZANY!!!!
			<button @click="$emit('endTest')">Wroć do bazy pytań</button>
		</div>
		<wnl-quiz-list
			:allQuestions="questions"
			:getReaction="getReaction"
			:isComplete="isComplete"
			:isProcessing="false"
			module="questions"
			@selectAnswer="onSelectAnswer"
			@checkQuiz="$emit('checkQuiz')"
		/>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
</style>

<script>
	import {isEmpty} from 'lodash'

	import QuizList from 'js/components/quiz/QuizList'
	import QuizTimer from 'js/components/quiz/QuizTimer'

	export default {
		name: 'QuestionsTest',
		props: ['questions', 'time', 'onSelectAnswer', 'onCheckQuiz', 'getReaction', 'testResults'],
		components: {
			'wnl-quiz-list': QuizList,
			'wnl-quiz-timer': QuizTimer
		},
		computed: {
			isComplete() {
				return !isEmpty(this.testResults)
			},
		},
		mounted() {
			!this.isComplete && this.$refs.timer.startTimer()
		}
	}
</script>
