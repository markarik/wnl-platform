<template>
	<div class="wnl-quiz-list" v-if="isLoaded">
		<wnl-quiz-question v-for="question in questions"
			:index="question.index"
			:answers="question.answers"
			:text="question.text"
		></wnl-quiz-question>
		<p class="has-text-centered" v-if="!isComplete">
			<a class="button is-primary" :class="{'is-loading': isProcessing}" @click="verify">
				Sprawdź pytania
			</a>
		</p>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-quiz-list
		border-top: $border-light-gray
		margin: $margin-big 0
		padding-top: $margin-big
</style>

<script>
	import _ from 'lodash'
	import QuizQuestion from 'js/components/course/screens/quiz/QuizQuestion.vue'
	import { mapGetters, mapActions } from 'vuex'
	import { scrollToTop } from 'js/utils/animations'

	export default {
		name: 'QuizList',
		components: {
			'wnl-quiz-question': QuizQuestion,
		},
		computed: {
			total() {
				return _.size(this.questions)
			},
			questions() {
				if (this.isComplete) {
					return this.getQuestions
				}

				return this.getUnresolved
			},
			...mapGetters('quiz', [
				'isComplete',
				'isLoaded',
				'isProcessing',
				'getUnresolved',
				'getQuestions',
			])
		},
		methods: {
			...mapActions('quiz', [
				'checkQuiz',
			]),
			verify() {
				this.checkQuiz().then(() => scrollToTop())
			},
		},
	}
</script>
