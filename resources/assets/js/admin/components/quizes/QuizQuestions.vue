<template>
	<div class="quiz-questions">
		<form class="quiz-question-search" v-on:submit.prevent>
			<div class="field has-addons">
				<div class="control">
					<input class="input" type="text" placeholder="Numer pytania" v-model="questionId">
				</div>
				<div class="control">
					<router-link :to="{name: 'quiz-editor', params: { quizId: questionId }}" class="button is-info">
						Szukaj!
					</router-link>
				</div>
			</div>
			<div class="field has-addons">
				<div class="control">
					<router-link :to="{name: 'quiz-creator'}" class="button is-info">
						Utwórz nowe pytanie
					</router-link>
				</div>
			</div>
		</form>
		<div class="quiz-question-editor">
			<router-view></router-view>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.quiz-questions
		display: flex

	.quiz-question-search
		width: 200px

	.quiz-question-editor
		flex: 1 auto
</style>

<script>
	export default {
		name: 'QuizQuestions',
		data() {
			return {
				questionId: ''
			}
		}
	}
</script>
