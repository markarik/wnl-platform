<template>
	<div>
		<h3>Twoja kolekcja pytań kontrolnych</h3>
		<div class="notification margin top"><strong>Kolekcje już wkrótce będzie można łatwo przeszukiwać m.in.&nbsp;po&nbsp;przedmiotach oraz słowach kluczowych, więc będą służyć jako wartościowe narzędzie do powtórek!</strong> Dlatego zachęcamy Cię gorąco do zapisywania w&nbsp;Twoich Kolekcjach slajdów, pytań oraz pytań kontrolnych na bieżąco!</div>
		<wnl-quiz-list v-if="isLoaded" readOnly="true"></wnl-quiz-list>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>

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
