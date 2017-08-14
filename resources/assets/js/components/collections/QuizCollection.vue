<template>
	<div class="collections-quiz">
		<p class="title is-4">Zapisane pytania kontrolne ({{howManyQuestions}})</p>
		<div v-if="isLoaded || howManyQuestions === 0">
			<wnl-quiz-widget
				v-if="howManyQuestions > 0"
				:questions="getQuestions"
				@changeQuestion="performChangeQuestion"
				@verify="resolveQuestion"
			></wnl-quiz-widget>
			<div v-else class="notification has-text-centered">
				W temacie <span class="metadata">{{rootCategoryName}} <span class="icon is-small"><i class="fa fa-angle-right"></i></span> {{categoryName}}</span> nie ma jeszcze zapisanych pytań kontrolnych. Możesz łatwo to zmienić klikając na <span class="icon is-small"><i class="fa fa-star-o"></i></span> <span class="metadata">ZAPISZ</span> przy wybranym pytaniu!
			</div>
		</div>
		<wnl-text-loader v-else></wnl-text-loader>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.collections-quiz
		padding: $margin-base 0
</style>

<script>
	import {mapActions, mapGetters} from 'vuex'

	import QuizWidget from 'js/components/quiz/QuizWidget'

	export default {
		name: 'QuizCollection',
		components: {
			'wnl-quiz-widget': QuizWidget,
		},
		props: ['categoryName', 'rootCategoryName', 'quizQuestionsIds'],
		computed: {
			...mapGetters('quiz', ['isLoaded', 'getQuestions']),
			howManyQuestions() {
				return this.quizQuestionsIds.length
			},
		},
		methods: {
			...mapActions('quiz', ['shuffleAnswers', 'changeQuestion', 'resolveQuestion']),
			performChangeQuestion(index) {
				this.shuffleAnswers({id: this.getQuestions[index].id})
				this.changeQuestion(index)
			}
		}
	}
</script>
