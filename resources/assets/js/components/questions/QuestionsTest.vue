<template>
	<div class="wnl-app-layout">
		{{ time }}
		<wnl-quiz-list
			:allQuestions="questions"
		/>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
</style>

<script>
import QuizList from 'js/components/quiz/QuizList'

export default {
	props: ['questions', 'time'],
	components: {
		'wnl-quiz-list': QuizList
	}
}
</script>
