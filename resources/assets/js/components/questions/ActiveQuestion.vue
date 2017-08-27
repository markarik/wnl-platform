<template>
	<div class="wnl-quiz-widget">
		<div class="quiz-widget-controls">
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
			v-if="currentQuestion"
			:class="`quiz-question-${currentQuestion.id}`"
			:id="currentQuestion.id"
			:question="currentQuestion"
			:showComments="true"
			:getReaction="getReaction"
			:module="module"
			@selectAnswer="selectAnswer"
		></wnl-quiz-question>
		<p class="has-text-centered">
			<a v-if="!currentQuestion.isResolved" class="button is-primary" :disabled="isSubmitDisabled" @click="verify">
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
		name: 'ActiveQuestion',
		components: {
			'wnl-quiz-question': QuizQuestion,
		},
		props: {
			currentQuestion: {
				type: Object,
				default: {},
			},
			getReaction: {
				default: () => {},
				type: Function,
			},
			module: {
				type: String,
				default: 'questions'
			}
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
				return this.currentQuestion.selectedAnswer !== null
			},
			isSubmitDisabled() {
				return !this.hasAnswer
			},
			displayResults() {
				return this.currentQuestion.isResolved
			},
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
			selectAnswer(data) {
				this.$emit('selectAnswer', data)
			}
		},
	}
</script>
