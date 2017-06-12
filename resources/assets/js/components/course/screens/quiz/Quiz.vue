<template>
	<div class="wnl-quiz">
		<div v-if="!isComplete">
			<p class="title is-5">
				Zanim zakończysz tę lekcję, sprawdź swoją wiedzę z wczorajszej! <wnl-emoji name="thinking_face"></wnl-emoji>
			</p>
			<p class="big">
				Po każdym podejściu, na ekranie pozostaną tylko błędnie rozwiązane pytania. Aby zakończyć test, odpowiadasz do skutku! Żeby nie było zbyt łatwo, kolejność odpowiedzi będzie każdorazowo zmieniana. Powodzenia!
			</p>
			<wnl-quiz-list v-if="isLoaded"></wnl-quiz-list>
			<wnl-text-loader class="margin vertical" v-else></wnl-text-loader>
		</div>
		<div v-else>
			<p class="title is-4 has-text-centered">
				Gratulacje! <wnl-emoji name="tada"></wnl-emoji>
			</p>
			<p class="big">Wszystkie pytania rozwiązane poprawnie! Możesz teraz sprawdzić poprawne odpowiedzi, oraz procentowy rozkład wyborów innych uczestników.</p>
			<wnl-quiz-summary></wnl-quiz-summary>
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

	import QuizList from 'js/components/course/screens/quiz/QuizList'
	import QuizSummary from 'js/components/course/screens/quiz/QuizSummary'
	import { mapActions, mapGetters } from 'vuex'

	export default {
		name: 'Quiz',
		components: {
			'wnl-quiz-list': QuizList,
			'wnl-quiz-summary': QuizSummary,
		},
		props: ['screenData'],
		computed: {
			...mapGetters('quiz', ['isComplete', 'isLoaded'])
		},
		methods: {
			...mapActions('quiz', ['setupQuestions', 'destroyQuiz']),
			setup() {
				let meta = this.screenData.meta
				if (!_.isObject(meta)) {
					meta = JSON.parse(meta)
				}

				this.setupQuestions(meta.resources[0])
			},
		},
		mounted() {
			this.setup()
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
