<template>
	<div>
		<p class="title is-5">
			Zanim zakończysz tę lekcję, sprawdź swoją wiedzę z wczorajszej! <wnl-emoji name="thinking_face"></wnl-emoji>
		</p>
		<p class="big">
			Po każdym podejściu, na ekranie pozostaną tylko błędnie rozwiązane pytania. Aby zakończyć test, odpowiadasz do skutku! Żeby nie było zbyt łatwo, kolejność odpowiedzi będzie każdorazowo zmieniana. Powodzenia!
		</p>
		<div class="wnl-quiz">
			<wnl-quiz-question v-for="question in getUnresolved" v-if="isLoaded"
				:index="question.index"
				:answers="question.answers"
				:text="question.text"
			></wnl-quiz-question>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-quiz
		border-top: $border-light-gray
		margin: $margin-big 0
		padding-top: $margin-big
</style>

<script>
	import _ from 'lodash'
	import QuizQuestion from 'js/components/course/screens/quiz/QuizQuestion.vue'
	import { mapGetters, mapActions } from 'vuex'

	export default {
		name: 'Quiz',
		components: {
			'wnl-quiz-question': QuizQuestion,
		},
		props: ['screenData'],
		computed: {
			total() {
				return _.size(this.questions)
			},
			...mapGetters('quiz', [
				'isLoaded',
				'getUnresolved',
			])
		},
		methods: {
			...mapActions('quiz', [
				'setupQuestions'
			])
		},
		mounted() {
			this.setupQuestions(this.screenData.meta.resources[0])
		}
	}
</script>
