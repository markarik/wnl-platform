<template>
	<div class="wnl-quiz-list" :class="{'has-errors': hasErrors, 'has-header': !plainList}">
		<p v-if="!plainList && isComplete" class="has-text-centered margin vertical">
			<a class="button is-primary is-outlined" @click="$emit('resetState')">Rozwiąż pytania ponownie</a>
		</p>

		<p class="title is-5" v-if="!plainList && !displayResults">Pozostało pytań: {{howManyLeft}}</p>
		<wnl-quiz-question v-for="(question, index) in questions"
			:module="module"
			:class="`quiz-question-${question.id}`"
			:question="question"
			:index="index"
			:isQuizComplete="isComplete"
			:key="question.id"
			:readOnly="readOnly"
			:getReaction="getReaction"
			@selectAnswer="onSelectAnswer"
		></wnl-quiz-question>
		<p class="has-text-centered" v-if="!plainList && !displayResults">
			<a class="button is-primary" :class="{'is-loading': isProcessing}" @click="verify">
				Sprawdź wyniki
			</a>
		</p>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-quiz-list.has-header
		border-top: $border-light-gray
		margin: $margin-big 0
		padding-top: $margin-base
</style>

<script>
	import _ from 'lodash'
	import QuizQuestion from 'js/components/quiz/QuizQuestion.vue'
	import { mapGetters, mapActions } from 'vuex'
	import { scrollToTop, scrollToElement } from 'js/utils/animations'
	import { swalConfig } from 'js/utils/swal'

	export default {
		name: 'QuizList',
		components: {
			'wnl-quiz-question': QuizQuestion,
		},
		props: ['readOnly', 'allQuestions', 'getReaction', 'module', 'isComplete', 'isProcessing', 'plainList'],
		data() {
			return {
				hasErrors: false,
			}
		},
		computed: {
			questions() {
				if (this.isComplete) {
					return this.allQuestions
				}

				return this.questionsUnresolved
			},
			questionsUnresolved() {
				return this.allQuestions.filter((question) => !question.isResolved)
			},
			questionsUnaswered() {
				return _.filter(this.allQuestions, (question) => {
					return !_.isNumber(question.selectedAnswer)
				})
			},
			displayResults() {
				return this.isComplete || this.readOnly || !this.allQuestions.length
			},
			howManyLeft() {
				return `${_.size(this.questionsUnresolved)}/${_.size(this.allQuestions)}`
			},
			unansweredAlert() {
				return {
					text: 'Aby zakończyć test, musisz rozwiązać poprawnie na wszystkie pytania, więc spróbuj odpowiedzieć na każde.',
					timer: 5000,
					title: 'Brakuje odpowiedzi',
					type: 'warning',
				}
			},
			tryAgainAlert() {
				return {
					text: `Pozostałe pytania do rozwiązania: ${this.questionsUnresolved.length}`,
					title: 'Spróbuj jeszcze raz!',
					type: 'info',
				}
			},
			successAlert() {
				return {
					text: 'Wszystkie pytania rozwiązane poprawnie!',
					title: 'Gratulacje!',
					type: 'success',
				}
			},
		},
		methods: {
			confirmQuizEnd(unanswered) {
				const config = swalConfig({
					confirmButtonText: this.$t('questions.solving.confirm.yes'),
					cancelButtonText: this.$t('questions.solving.confirm.no'),
					reverseButtons: true,
					showCancelButton: true,
					showConfirmButton: true,
					text: this.$t('questions.solving.confirm.unanswered', {
						count: unanswered
					}),
					title: this.$t('questions.solving.confirm.title'),
					type: 'question',
				})

				return new Promise((resolve, reject) => {
					this.$swal(config)
						.then(() => resolve(), () => reject())
						.catch(e => reject())
				})
			},
			verify() {
				const unanswered = this.questionsUnaswered.length
				if (!this.plainList && unanswered > 0) {
					this.hasErrors = true

					this.confirmQuizEnd(unanswered)
						.then(() => false)
						.catch(() => this.$emit('checkQuiz', true))

					this.scrollToFirstUnanswered()
					return false
				}

				this.hasErrors = false
				this.$emit('checkQuiz')
			},
			showAlert() {
				let alertOptions = this.isComplete ? this.successAlert : this.tryAgainAlert,
					firstElement = this.isComplete ? _.head(this.allQuestions) : _.head(this.questionsUnresolved)

				this.$swal(this.getAlertConfig(alertOptions))
					.catch(e => false)

				scrollToElement(this.getQuestionElement(firstElement))
			},
			getAlertConfig(options = {}) {
				const defaults = {
					showConfirmButton: false,
					timer: 3500,
				}

				return swalConfig(_.merge(defaults, options))
			},
			getQuestionElement(resource) {
				return this.$el.getElementsByClassName(`quiz-question-${resource.id}`)[0]
			},
			onSelectAnswer(data) {
				this.$emit('selectAnswer', data)
			},
			scrollToFirstUnanswered() {
				scrollToElement(this.getQuestionElement(_.head(this.questionsUnaswered)))
			},
		},
	}
</script>
