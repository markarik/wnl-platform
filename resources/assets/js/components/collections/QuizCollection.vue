<template>
	<div class="collections-quiz">
		<p class="title is-4">Zapisane pytania kontrolne</p>
		<wnl-quiz-widget v-if="isLoaded"></wnl-quiz-widget>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.collections-quiz
		padding: $margin-base
</style>

<script>
	import {mapActions, mapGetters} from 'vuex'

	import QuizWidget from 'js/components/course/screens/quiz/QuizWidget'

	export default {
		name: 'QuizCollection',
		components: {
			'wnl-quiz-widget': QuizWidget,
		},
		computed: {
			...mapGetters('collections', ['quizQuestionsIds']),
			...mapGetters('quiz', ['isLoaded']),
		}
	}
</script>
