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
				'getQuestions',
			]),
			total() {
				return _.size(this.questions)
			},
			tryAgainAlert() {
				return {
					showConfirmButton: false,
					text: `Pozostałe pytania do rozwiązania: ${this.getUnresolved.length}`,
					timer: 2000,
					title: 'Spróbuj jeszcze raz!',
					type: 'info',
				}
			},
			successAlert() {
				return {
					showConfirmButton: false,
					text: 'Wszystkie pytania rozwiązane poprawnie!',
					timer: 2000,
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
				this.checkQuiz().then(() => {
					const alert = this.isComplete ? this.successAlert : this.tryAgainAlert
					$wnl.logger.debug(alert)
					this.$swal(swalConfig(alert))
					scrollToTop()
				})
			},
		},
	}
</script>
