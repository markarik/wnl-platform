<template>
	<div>
		<h3>Twoja kolekcja pytań kontrolnych</h3>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>

</style>

<script>
	import {mapActions, mapGetters} from 'vuex'

	export default {
		name: 'QuizCollection',
		computed: {
			...mapGetters('collections', ['quizQuestionsIds']),
		},
		methods: {
			...mapActions('quiz', ['fetchQuestionsCollection']),
		},
		mounted() {
			this.fetchQuestionsCollection(this.quizQuestionsIds)
		},
	}
</script>
