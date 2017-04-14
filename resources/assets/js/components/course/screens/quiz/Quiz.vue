<template>
	<div>
		<wnl-quiz-question v-for="question in getUnresolved" v-if="isLoaded"
			:answers="question.answers"
			:text="question.text"
		></wnl-quiz-question>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>

</style>

<script>
	import _ from 'lodash'
	import QuizQuestion from 'js/components/course/screens/quiz/QuizQuestion.vue'
	import { mapGetters, mapActions } from 'vuex'

	export default {
		name: 'Quiz',
		components: {
			'wnl-quiz-question': QuizQuestion,
		},
		props: ['screenData'],
		computed: {
			total() {
				return _.size(this.questions)
			},
			...mapGetters('quiz', [
				'isLoaded',
				'getUnresolved',
			])
		},
		methods: {
			...mapActions('quiz', [
				'setupQuestions'
			])
		},
		mounted() {
			this.setupQuestions(this.screenData.meta.resources[0])
		}
	}
</script>
