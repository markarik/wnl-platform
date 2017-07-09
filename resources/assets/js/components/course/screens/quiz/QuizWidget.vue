<template>
	<div class="wnl-quiz-widget">
		<wnl-quiz-question
			:class="`quiz-question-${currentQuestion.id}`"
			:id="currentQuestion.id"
			:index="index"
			:answers="currentQuestion.quiz_answers"
			:text="currentQuestion.text"
			:total="currentQuestion.total_hits"
			@answerSelected="verify"
			v-if="currentQuestion"
		></wnl-quiz-question>
		<p class="has-text-centered" v-if="!displayResults">
			<a class="button is-primary" :class="{'is-loading': isProcessing}" @click="verify">
				Sprawdź odpowiedź
			</a>
		</p>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

</style>

<script>
	import _ from 'lodash'
	import QuizQuestion from 'js/components/course/screens/quiz/QuizQuestion.vue'
	import { mapGetters, mapActions } from 'vuex'
	import { scrollToElement } from 'js/utils/animations'
	import { swalConfig } from 'js/utils/swal'

	export default {
		name: 'QuizWidget',
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
				'getUnanswered',
				'getQuestions',
				'hasQuestions'
			]),
			currentQuestion() {
				console.log()
				return {}
			},
			displayResults() {
				return this.isComplete || this.readOnly || !this.hasQuestions
			},
			howManyLeft() {
				return `${_.size(this.getUnresolved)}/${_.size(this.getQuestions)}`
			},
			questions() {
				if (this.isComplete) {
					return this.getQuestions
				}

				return this.getUnresolved
			},
		},
		methods: {
			...mapActions('quiz', [
				'checkQuiz',
				'resetState',
			]),
			verify() {
				// if (this.getUnanswered.length > 0) {
				// 	this.hasErrors = true
				// 	this.$swal(this.getAlertConfig(this.unansweredAlert))
				// 		.catch(e => {
				// 			// It's a bug in the library. It throws an exception
				// 			// if a person closes a timed modal with a click.
				// 			$wnl.logger.debug('SweetAlert2 exception', e)
				// 		})
				//
				// 	scrollToElement(this.getQuestionElement(_.head(this.getUnanswered)))
				// 	return false
				// }
				//
				// this.hasErrors = false
				// this.dispatchCheckQuiz()
			},
			dispatchCheckQuiz() {
				this.checkQuiz().then(() => {
					let alertOptions = this.isComplete ? this.successAlert : this.tryAgainAlert,
						firstElement = this.isComplete ? _.head(this.getQuestions) : _.head(this.getUnresolved)

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
