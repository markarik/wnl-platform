<template>
	<div class="quiz-editor">
		<p class="title is-5">Edycja zestawu</p>
		<div class="field">
			<div class="control">
				<label class="label">
					<span>Pytanie</span>
					<input type="text" class="input">
				</label>
			</div>
		</div>
		<div class="field">
			<div class="control">
				<label class="label">
					<span>Odpowiedź 1</span>
					<input type="text" class="input">
				</label>
				<label class="label">
					<span>Prawidłowa?</span>
					<input type="checkbox">
				</label>
			</div>
		</div>
		<div class="field">
			<div class="control">
				<label class="label">
					<span>Odpowiedź 2</span>
					<input type="text" class="input">
				</label>
				<label class="label">
					<span>Prawidłowa?</span>
					<input type="checkbox">
				</label>
			</div>
		</div>
		<div class="field">
			<div class="control">
				<label class="label">
					<span>Odpowiedź 3</span>
					<input type="text" class="input">
				</label>
				<label class="label">
					<span>Prawidłowa?</span>
					<input type="checkbox">
				</label>
			</div>
		</div>
		<div class="field">
			<div class="control">
				<label class="label">
					<span>Odpowiedź 4</span>
					<input type="text" class="input">
				</label>
				<label class="label">
					<span>Prawidłowa?</span>
					<input type="checkbox">
				</label>
			</div>
		</div>
		<p class="title is-5 margin top">Pytania</p>
		<div>
			<a class="quiz-add-question button is-primary is-outlined">
				Dodaj pytanie
			</a>
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
	import { mapActions } from 'vuex';

	export default {
		name: 'QuizesEditor',
		methods: {
			...mapActions(['getQuizQuestion'])
		},
		created() {
			debugger
			this.getQuizQuestion(this.$route.params.quizId);
		}
	}
</script>
