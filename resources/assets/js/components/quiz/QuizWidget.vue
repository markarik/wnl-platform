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
			:question="currentQuestion"
			:showComments="true"
			:getReaction="getReaction"
			:module="module"
			@selectAnswer="selectAnswer"
			@answerDoubleclick="onAnswerDoubleClick"
			@userEvent="proxyUserEvent"
			v-if="currentQuestion"
		></wnl-quiz-question>
		<p class="has-text-centered">
			<a v-if="!currentQuestion.isResolved" class="button is-primary" :disabled="isSubmitDisabled" @click="verify">
				Sprawdź odpowiedź
			</a>
			<a v-else-if="hasOtherQuestions" class="button is-primary is-outlined" @click="nextQuestion()">
				Następne
			</a>
		</p>
		<div class="other-questions" v-if="hasOtherQuestions">
			<p class="notification small">
				Możesz wybrać dowolne pytanie z listy klikając na jego tytuł
			</p>
			<wnl-quiz-question
				v-for="(question, index) in otherQuestions"
				:headerOnly="true"
				:question="question"
				:class="`clickable quiz-question-${question.id}`"
				:key="index"
				:getReaction="getReaction"
				:module="module"
				@headerClicked="selectQuestionFromList(index)"
				@selectAnswer="selectAnswer"
				@answerDoubleclick="onAnswerDoubleClick"
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
	import { mapGetters } from 'vuex'

	import QuizQuestion from 'js/components/quiz/QuizQuestion.vue'
	import { scrollToElement } from 'js/utils/animations'
	import emits_events from 'js/mixins/emits-events';
	import feature_components from 'js/consts/events_map/feature_components.json';

	export default {
		name: 'QuizWidget',
		components: {
			'wnl-quiz-question': QuizQuestion,
		},
		mixins: [emits_events],
		props: {
			isSingle: {
				default: false,
				type: Boolean,
			},
			readOnly: {
				default: false,
				type: Boolean,
			},
			questions: {
				type: Array,
				default: [],
			},
			getReaction: {
				default: () => {},
				type: Function,
			},
			module: {
				type: String,
				default: 'quiz'
			}
		},
		data() {
			return {
				hasErrors: false,
				allowDoubleclick: true
			}
		},
		computed: {
			...mapGetters(['isMobile']),
			currentQuestion() {
				return this.questions[0]
			},
			otherQuestions() {
				return _.tail(this.questions) || []
			},
			lastIndex() {
				return this.questions.length - 1
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
			verify() {
				if (this.hasAnswer) {
					this.$emit('verify', this.currentQuestion.id)
				}
			},
			nextQuestion() {
				this.$emit('changeQuestion', 1)
				scrollToElement(this.$el, 75)
			},
			previousQuestion() {
				this.$emit('changeQuestion', this.lastIndex)
				scrollToElement(this.$el, 75)
			},
			selectQuestionFromList(index) {
				const fullIndex = index + 1
				this.$emit('changeQuestion', fullIndex)
				scrollToElement(this.$el, 75)
			},
			selectAnswer({answer}) {
				this.allowDoubleclick = false
				this.$emit('selectAnswer', {
					id: this.currentQuestion.id,
					answer
				})
				this.timeout = setTimeout(() => {
					this.allowDoubleclick = true
				}, 500)
			},
			onAnswerDoubleClick({answer}) {
				this.allowDoubleclick && this.displayResults && this.nextQuestion()
			},
		},
		watch: {
			'currentQuestion.id'() {
				this.currentQuestion.id && this.emitUserEvent({
					feature_component: feature_components.quiz_question.value,
					action: feature_components.quiz_question.actions.open.value,
					target: this.currentQuestion.id
				})
			}
		}
	}
</script>
