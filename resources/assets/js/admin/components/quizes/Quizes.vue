<template>
	<div class="quizes">
		<div class="quizes-list">
			<form>
				<input v-model="questionId">
				<router-link :to="{name: 'quiz-editor', params: { quizId: questionId }}">
					Szukej!
				</router-link>
			</form>
		</div>
		<div class="quiz-editor">
			<router-view></router-view>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.quizes
		display: flex

	.quizes-list
		width: 200px

	.quiz-editor
		flex: 1 auto
</style>

<script>
	export default {
		name: 'Quizes',
		data() {
			return {
				questionId: ''
			}
		}
	}
</script>
