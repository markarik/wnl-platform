<template>
	<div class="wnl-quiz">
		<div v-if="isLoaded && displayResults">
			<p class="title is-4 has-text-centered">
				Gratulacje! <wnl-emoji name="tada"></wnl-emoji>
			</p>
			<p class="big">Wszystkie pytania rozwiązane poprawnie! Możesz teraz sprawdzić poprawne odpowiedzi, oraz procentowy rozkład wyborów innych uczestników.</p>
			<wnl-quiz-summary :showAlert="showAlert"></wnl-quiz-summary>
		</div>
		<div v-else-if="emptyQuizSet" class="has-text-centered">
			Oho, wygląda że nie ma pytań kontrolnych dla tej lekcji.
		</div>
		<div v-else>
			<p class="title is-5">
				Zanim zakończysz tę lekcję, sprawdź swoją wiedzę z wczorajszej! <wnl-emoji name="thinking_face"></wnl-emoji>
			</p>
			<p class="big">
				Po każdym podejściu, na ekranie pozostaną tylko błędnie rozwiązane pytania. Aby zakończyć test, odpowiadasz do skutku! Żeby nie było zbyt łatwo, kolejność odpowiedzi będzie każdorazowo zmieniana. Powodzenia!
			</p>
			<p class="has-text-centered" v-if="isAdmin">
				<a class="button is-primary is-outlined" @click="autoResolve">
					Rozwiąż wszystkie pytania
				</a>
			</p>
			<wnl-quiz-list v-if="isLoaded"
				module="quiz"
				ref="quizList"
				:allQuestions="getQuestionsWithAnswers"
				:canEndQuiz="canEndQuiz"
				:getReaction="getReaction"
				:isProcessing="isProcessing"
				:isComplete="isComplete"
				@selectAnswer="onAnswerSelect"
				@resetState="resetState"
				@checkQuiz="onCheckQuiz"
			/>
			<wnl-text-loader class="margin vertical" v-else></wnl-text-loader>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

	.wnl-quiz
		margin: $margin-big 0
</style>

<script>
	import _ from 'lodash'
	import { mapActions, mapGetters } from 'vuex'

	import QuizList from 'js/components/quiz/QuizList'
	import QuizSummary from 'js/components/quiz/QuizSummary'
	import {scrollToTop} from 'js/utils/animations'

	export default {
		name: 'Quiz',
		components: {
			'wnl-quiz-list': QuizList,
			'wnl-quiz-summary': QuizSummary,
		},
		data() {
			return {
				showAlert: false,
				emptyQuizSet: false
			}
		},
		props: ['screenData', 'readOnly'],
		computed: {
			...mapGetters('quiz', [
				'getAttempts',
				'getQuestionsWithAnswers',
				'getReaction',
				'isComplete',
				'isLoaded',
				'isProcessing',
			]),
			...mapGetters(['isAdmin']),
			canEndQuiz() {
				return this.getAttempts.length > 2
			},
			displayResults() {
				return this.readOnly || this.isComplete
			},
		},
		methods: {
			...mapActions('quiz', ['setupQuestions', 'destroyQuiz', 'autoResolve', 'commitSelectAnswer', 'resetState', 'checkQuiz']),
			setup() {
				let meta = this.screenData.meta
				if (!_.isObject(meta)) {
					meta = JSON.parse(meta)
				}

				if (!meta.resources) {
					this.emptyQuizSet = true;
				} else {
					this.setupQuestions(meta.resources[0])
				}
			},
			onAnswerSelect(data) {
				if (!this.isComplete) {
					this.commitSelectAnswer(data)
				}
			},
			onCheckQuiz(force = false) {
				this.checkQuiz(force).then(() => {
					if (this.isComplete) {
						if (!force) {
							this.showAlert = true
						}
						scrollToTop()
					} else {
						this.$refs.quizList.showAlert()
					}
				})
			}
		},
		mounted() {
			this.setup()
		},
		beforeDestroy() {
			this.destroyQuiz()
		},
		watch: {
			'screenData' (newValue, oldValue) {
				if (oldValue.type === 'quiz' && newValue.type === 'quiz') {
					this.destroyQuiz()
						.then(() => {
							this.setup()
						})
				}
			}
		}
	}
</script>
