<template>
	<div class="collections-quiz">
		<p class="title is-4">Zapisane pytania kontrolne <span v-if="isLoaded">({{howManyQuestions}})</span></p>
		<div v-if="isLoaded">
			<wnl-quiz-widget v-if="howManyQuestions > 0"></wnl-quiz-widget>
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

	import QuizWidget from 'js/components/course/screens/quiz/QuizWidget'

	export default {
		name: 'QuizCollection',
		components: {
			'wnl-quiz-widget': QuizWidget,
		},
		props: ['categoryName', 'rootCategoryName'],
		computed: {
			...mapGetters('collections', ['quizQuestionsIds']),
			...mapGetters('quiz', ['isLoaded', 'getQuestions']),
			howManyQuestions() {
				return this.getQuestions.length
			},
		}
	}
</script>
