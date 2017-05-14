<template>
	<div class="quiz-editor">
		<p class="title is-5">Edycja zestawu</p>
		<div class="field">
			<div class="control">
				<label class="label">
					Nazwa zestawu
				</label>
				<input type="text" class="input">
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
	export default {
		name: 'QuizesEditor',
	}
</script>
