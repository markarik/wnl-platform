<template>
	<div class="wnl-quiz-list" v-if="isLoaded">
		<p class="title is-5" v-if="!isComplete">Pozostało pytań: {{howManyLeft}}</p>
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
	import { swalConfig } from 'js/utils/swal'

	export default {
		name: 'QuizList',
		components: {
			'wnl-quiz-question': QuizQuestion,
		},
		computed: {
			...mapGetters('quiz', [
				'isComplete',
				'isLoaded',
				'isProcessing',
				'getUnresolved',
				'getUnanswered',
				'getQuestions',
			]),
			howManyLeft() {
				return `${_.size(this.getUnresolved)}/${_.size(this.getQuestions)}`
			},
			unansweredAlert() {
				return {
					title: 'Brakuje odpowiedzi',
					timer: 3000,
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
					return this.getQuestions
				}

				return this.getUnresolved
			},
		},
		methods: {
			...mapActions('quiz', [
				'checkQuiz',
			]),
			verify() {
				if (this.getUnanswered.length > 0) {
					this.$swal(this.getAlertConfig(this.unansweredAlert))
					return false
				}

				this.dispatchCheckQuiz()
			},
			dispatchCheckQuiz() {
				this.checkQuiz().then(() => {
					const alertOptions = this.isComplete ? this.successAlert : this.tryAgainAlert
					this.$swal(this.getAlertConfig(alertOptions))
					scrollToTop()
				})
			},
			getAlertConfig(options = {}) {
				const defaults = {
					showConfirmButton: false,
					timer: 2000,
				}

				return swalConfig(_.merge(defaults, options))
			}
		},
	}
</script>
