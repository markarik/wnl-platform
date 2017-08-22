<template>
	<div class="wnl-app-layout">
		{{ time }}
		<wnl-quiz-list
			:allQuestions="reactiveQuestions"
			:isComplete="false"
			:isProcessing="false"
			@selectAnswer="onSelectAnswer"
		/>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
</style>

<script>
import QuizList from 'js/components/quiz/QuizList'
import Vue from 'vue'

export default {
	props: ['questions', 'time', 'onSelectAnswer'],
	data() {
		return {
			allQuestions: []
		}
	},
	computed: {
		reactiveQuestions() {
			return this.allQuestions.length ? this.allQuestions : this.questions
		}
	},
	components: {
		'wnl-quiz-list': QuizList
	}
}
</script>
