<template>
	<div class="collections-quiz">
		<h3>Twoja kolekcja pytań kontrolnych</h3>
		<wnl-quiz-list v-if="isLoaded"></wnl-quiz-list>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.collections-quiz
		padding: $margin-base
</style>

<script>
	import {mapActions, mapGetters} from 'vuex'

	import QuizList from 'js/components/course/screens/quiz/QuizList'

	export default {
		name: 'QuizCollection',
		components: {
			'wnl-quiz-list': QuizList,
		},
		computed: {
			...mapGetters('collections', ['quizQuestionsIds']),
			...mapGetters('quiz', ['isLoaded']),
		}
	}
</script>
