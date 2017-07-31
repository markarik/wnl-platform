<template>
	<div class="wnl-quiz-widget">
		<div v-if="!isSingle" class="quiz-widget-controls">
			<div class="widget-control">
				<a class="small unselectable" @click="previousQuestion()">
					<span class="icon is-small"><i class="fa fa-angle-left"></i></span> Poprzednie
				</a>
			</div>
			<div class="widget-control">
				<a class="small unselectable" @click="nextQuestion()">
					Następne <span class="icon is-small"><i class="fa fa-angle-right"></i></span>
				</a>
			</div>
		</div>
		<wnl-quiz-question
			:class="`quiz-question-${currentQuestion.id}`"
			:id="currentQuestion.id"
			:answers="currentQuestion.quiz_answers"
			:text="currentQuestion.text"
			:total="currentQuestion.total_hits"
			:showComments="true"
			v-if="currentQuestion"
		></wnl-quiz-question>
		<p class="has-text-centered">
			<a v-if="!currentQuestion.isResolved" class="button is-primary" :disabled="isSubmitDisabled" @click="verify">
				Sprawdź odpowiedź
			</a>
			<a v-else-if="!isSingle" class="button is-primary is-outlined" @click="nextQuestion()">
				Następne
			</a>
		</p>
		<div class="other-questions" v-if="hasOtherQuestions">
			<p class="notification small">
				Możesz wybrać dowolne pytanie z listy klikając na jego tytuł
			</p>
			<wnl-quiz-question
				v-for="question, index in otherQuestions"
				:headerOnly="true"
				:answers="question.quiz_answers"
				:class="`clickable quiz-question-${currentQuestion.id}`"
				:key="index"
				:id="question.id"
				:text="question.text"
				:total="question.total_hits"
				@headerClicked="selectQuestionFromList(index)"
			></wnl-quiz-question>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.other-questions
		border-top: $border-light-gray
		margin-top: $margin-big
		padding-top: $margin-base

	.quiz-widget-controls
		display: flex
		justify-content: space-between
</style>

<script>
	import _ from 'lodash'
	import { mapGetters, mapActions } from 'vuex'

	import QuizQuestion from 'js/components/quiz/QuizQuestion.vue'
	import { scrollToElement } from 'js/utils/animations'
	import { swalConfig } from 'js/utils/swal'

	export default {
		name: 'QuizWidget',
		components: {
			'wnl-quiz-question': QuizQuestion,
		},
		props: {
			isSingle: {
				default: false,
				type: Boolean,
			},
			readOnly: {
				default: false,
				type: Boolean,
			},
		},
		data() {
			return {
				hasErrors: false,
			}
		},
		computed: {
			...mapGetters(['isMobile']),
			...mapGetters('quiz', [
				'getQuestions',
			]),
			currentQuestion() {
				return this.getQuestions[0]
			},
			otherQuestions() {
				return _.tail(this.getQuestions) || []
			},
			lastIndex() {
				return this.getQuestions.length - 1
			},
			hasAnswer() {
				return this.currentQuestion.selectedAnswer !== null
			},
			isSubmitDisabled() {
				return !this.hasAnswer
			},
			displayResults() {
				return this.currentQuestion.isResolved
			},
			hasOtherQuestions() {
				return this.otherQuestions.length > 0
			}
		},
		methods: {
			...mapActions('quiz', [
				'changeQuestion',
				'shuffleAnswers',
				'resolveQuestion',
				'resetState',
			]),
			verify() {
				if (this.hasAnswer) {
					this.resolveQuestion(this.currentQuestion.id)
				}
			},
			performChangeQuestion(index) {
				this.shuffleAnswers({id: this.getQuestions[index].id})
				this.changeQuestion(index)
				scrollToElement(this.$el, 75)
			},
			nextQuestion() {
				this.performChangeQuestion(1)
			},
			previousQuestion() {
				this.performChangeQuestion(this.lastIndex)
			},
			selectQuestionFromList(index) {
				const fullIndex = index + 1
				this.performChangeQuestion(fullIndex)
			},
		},
	}
</script>
