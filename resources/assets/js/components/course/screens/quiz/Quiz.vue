<template>
	<div class="wnl-quiz">
		<div v-if="!isComplete">
			<p class="title is-5">
				Zanim zakończysz tę lekcję, sprawdź swoją wiedzę z wczorajszej! <wnl-emoji name="thinking_face"></wnl-emoji>
			</p>
			<p class="big">
				Po każdym podejściu, na ekranie pozostaną tylko błędnie rozwiązane pytania. Aby zakończyć test, odpowiadasz do skutku! Żeby nie było zbyt łatwo, kolejność odpowiedzi będzie każdorazowo zmieniana. Powodzenia!
			</p>
			<wnl-quiz-list></wnl-quiz-list>
		</div>
		<div v-else>
			<p class="title is-5 has-text-centered">
				Gratulacje! <wnl-emoji name="tada"></wnl-emoji>
			</p>
			<p class="big">Pytania kontrolne na dziś masz już zaliczone!</p>
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
			...mapGetters('quiz', ['isComplete'])
		},
		methods: {
			...mapActions('quiz', ['setupQuestions'])
		},
		mounted() {
			this.setupQuestions(this.screenData.meta.resources[0])
		}
	}
</script>
