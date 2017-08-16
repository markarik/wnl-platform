<template>
	<div class="wnl-quiz-list" :class="{'has-errors': hasErrors}">
		<p v-if="isComplete" class="has-text-centered margin vertical">
			<a class="button is-primary is-outlined" @click="resetState">Rozwiąż pytania ponownie</a>
		</p>

		<p class="title is-5" v-if="!displayResults">Pozostało pytań: {{howManyLeft}}</p>
		<wnl-quiz-question v-for="(question, index) in questions"
			:class="`quiz-question-${question.id}`"
			:question="question"
			:index="index"
			:key="question.id"
			:readOnly="readOnly"
		></wnl-quiz-question>
		<p class="has-text-centered" v-if="!displayResults">
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
		padding-top: $margin-base
</style>

<script>
	import _ from 'lodash'
	import QuizQuestion from 'js/components/quiz/QuizQuestion.vue'
	import { mapGetters, mapActions } from 'vuex'
	import { scrollToElement } from 'js/utils/animations'
	import { swalConfig } from 'js/utils/swal'

	export default {
		name: 'QuizList',
		components: {
			'wnl-quiz-question': QuizQuestion,
		},
		props: ['readOnly'],
		data() {
			return {
				hasErrors: false,
			}
		},
		computed: {
			...mapGetters('quiz', [
				'isComplete',
				'isProcessing',
				'getUnresolved',
				'getUnresolvedWithAnswers',
				'getUnanswered',
				'getQuestionsWithAnswers',
				'hasQuestions'
			]),
			displayResults() {
				return this.isComplete || this.readOnly || !this.hasQuestions
			},
			howManyLeft() {
				return `${_.size(this.getUnresolved)}/${_.size(this.getQuestionsWithAnswers)}`
			},
			unansweredAlert() {
				return {
					title: 'Brakuje odpowiedzi',
					timer: 5000,
					text: 'Aby zakończyć test, musisz rozwiązać poprawnie na wszystkie pytania, więc spróbuj odpowiedzieć na każde.',
					type: 'warning',
				}
			},
			tryAgainAlert() {
				return {
					text: `Pozostałe pytania do rozwiązania: ${this.getUnresolved.length}`,
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
			questions() {
				if (this.isComplete) {
					return this.getQuestionsWithAnswers
				}

				return this.getUnresolvedWithAnswers
			},
		},
		methods: {
			...mapActions('quiz', [
				'checkQuiz',
				'resetState',
			]),
			verify() {
				if (this.getUnanswered.length > 0) {
					this.hasErrors = true
					this.$swal(this.getAlertConfig(this.unansweredAlert))
						.catch(e => {
							// It's a bug in the library. It throws an exception
							// if a person closes a timed modal with a click.
							$wnl.logger.debug('SweetAlert2 exception', e)
						})

					scrollToElement(this.getQuestionElement(_.head(this.getUnanswered)))
					return false
				}

				this.hasErrors = false
				this.dispatchCheckQuiz()
			},
			dispatchCheckQuiz() {
				this.checkQuiz().then(() => {
					let alertOptions = this.isComplete ? this.successAlert : this.tryAgainAlert,
						firstElement = this.isComplete ? _.head(this.getQuestionsWithAnswers) : _.head(this.getUnresolved)

					this.$swal(this.getAlertConfig(alertOptions))
						.catch(e => {
							// It's a bug in the library. It throws an exception
							// if a person closes a timed modal with a click.
							$wnl.logger.debug('SweetAlert2 exception', e)
						})

					scrollToElement(this.getQuestionElement(firstElement))
				})
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
			}
		},
	}
</script>
