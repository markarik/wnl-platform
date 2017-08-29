<template>
	<div class="wnl-active-question">
		<div class="active-question-controls">
			<div class="widget-control">
				<a class="small unselectable" @click="previousQuestion()">
					<span class="icon is-small"><i class="fa fa-angle-left"></i></span> Poprzednie
				</a>
			</div>
			<div class="widget-control">
				{{$t('questions.solving.current', {number: questionNumber})}}
				<span class="matched-count">{{allQuestionsCount}}</span>
			</div>
			<div class="widget-control">
				<a class="small unselectable" @click="nextQuestion()">
					Następne <span class="icon is-small"><i class="fa fa-angle-right"></i></span>
				</a>
			</div>
		</div>
		<wnl-quiz-question
			v-if="question"
			:class="`quiz-question-${question.id}`"
			:id="question.id"
			:question="question"
			:showComments="true"
			:getReaction="getReaction"
			:module="module"
			@selectAnswer="selectAnswer"
		/>
		<p class="has-text-centered">
			<a v-if="!question.isResolved" class="button is-primary" :disabled="!hasAnswer" @click="verify">
				Sprawdź odpowiedź
			</a>
			<a v-else class="button is-primary is-outlined" @click="nextQuestion()">
				Następne
			</a>
		</p>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-active-question
		padding-top: $margin-medium

	.active-question-controls
		display: flex
		font-size: $font-size-base
		justify-content: space-between
		margin-bottom: $margin-base

	.matched-count
		color: $color-green
</style>

<script>
	import _ from 'lodash'
	import { mapGetters, mapActions } from 'vuex'

	import QuizQuestion from 'js/components/quiz/QuizQuestion.vue'
	import { scrollToElement } from 'js/utils/animations'
	import { swalConfig } from 'js/utils/swal'

	export default {
		name: 'ActiveQuestion',
		components: {
			'wnl-quiz-question': QuizQuestion,
		},
		props: {
			allQuestionsCount: {
				default: 0,
				type: Number,
			},
			getReaction: {
				default: () => {},
				type: Function,
			},
			module: {
				type: String,
				default: 'questions'
			},
			question: {
				type: Object,
				default: {},
			},
			questionNumber: {
				type: Number,
				default: 0,
			},
		},
		data() {
			return {
				hasErrors: false,
			}
		},
		computed: {
			...mapGetters(['isMobile']),
			...mapGetters('questions', ['getQuestion']),
			hasAnswer() {
				return _.isNumber(this.question.selectedAnswer)
			},
			isSubmitDisabled() {
				return !this.hasAnswer
			},
			displayResults() {
				return this.question.isResolved
			},
		},
		methods: {
			nextQuestion() {
				this.$emit('changeQuestion', 1)
				scrollToElement(this.$el, 75)
			},
			previousQuestion() {
				this.$emit('changeQuestion', -1)
				scrollToElement(this.$el, 75)
			},
			selectAnswer(data) {
				data.answer === this.question.selectedAnswer
					? this.$emit('verify', this.question.id)
					: this.$emit('selectAnswer', data)
			},
			verify() {
				this.hasAnswer && this.$emit('verify', this.question.id)
			},
		},
	}
</script>
