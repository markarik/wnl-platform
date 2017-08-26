<template>
	<div class="quiz-editor">
		<p class="title is-5">Edycja zestawu</p>
		<div class="field">
			<div class="control">
				<label class="label">
					<span>Pytanie</span>
					<input type="text" class="input" :value="questionText">
				</label>
			</div>
		</div>
		<div class="field" v-for="(answer, index) in questionAnswers">
			<div class="control">
				<label class="label">
					<span>Odpowiedź {{index}}</span>
					<input type="text" class="input" :value="answer.text">
				</label>
				<label class="label">
					<span>Prawidłowa?</span>
					<input type="checkbox" :checked="answer.is_correct">
				</label>
			</div>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.quiz-editor
		max-width: 800px

	.quiz-add-question
		display: block
		margin: $margin-big 0
</style>

<script>
	import { mapActions, mapGetters } from 'vuex';

	export default {
		name: 'QuizesEditor',
		computed: {
			...mapGetters(['questionText', 'questionAnswers'])
		},
		methods: {
			...mapActions(['getQuizQuestion'])
		},
		created() {
			debugger
			this.getQuizQuestion(this.$route.params.quizId);
		}
	}
</script>
