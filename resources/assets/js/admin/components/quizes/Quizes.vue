<template>
	<div class="quizes">
		<div class="quizes-list">
			<p class="title is-5">Wybierz zestaw pytań</p>
			<div v-for="quiz in quizes" :key="quiz.id">
				<router-link :to="{name: 'quiz-editor', params: { quizId: quiz.id }}">
					{{ quiz.name }}
				</router-link>
			</div>
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
				quizes: [
					{
						id: 1,
						name: 'Quiz 1',
					},
					{
						id: 2,
						name: 'Quiz 2',
					},
				],
			}
		}
	}
</script>
