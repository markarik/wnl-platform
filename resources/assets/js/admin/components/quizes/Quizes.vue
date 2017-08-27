<template>
	<div class="quizes">
		<div class="quizes-list">
			<form>				
				<div class="field has-addons">
					<div class="control">
						<input class="input" type="text" placeholder="Numer pytania" v-model="questionId">
					</div>
					<div class="control">
						<router-link :to="{name: 'quiz-editor', params: { quizId: questionId }}" class="button is-info">
							Szukej!
						</router-link>
					</div>
				</div>
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
