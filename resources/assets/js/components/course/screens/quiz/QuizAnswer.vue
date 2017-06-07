<template>
	<li class="quiz-answer"
		:class="{
			'is-selected': isSelected,
			'is-correct': showCorrect,
			'is-hinted': hintCorrect,
		}"
		@click="$emit('answerSelected')"
	>
		<div class="quiz-answer-content">
			{{answer.text}}
		</div>
		<div class="quiz-answer-stats" v-if="isComplete">
			<span class="tag" :title="`${stats}% osób wybrało tę odpowiedź`">
				{{stats}}%
			</span>
		</div>
	</li>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>

</style>

<script>
	import { mapGetters } from 'vuex'

	import { isDebug } from 'js/utils/env'

	export default {
		name: 'QuizAnswer',
		props: ['answer', 'index', 'questionId', 'totalHits'],
		computed: {
			...mapGetters('quiz', [
				'isComplete',
				'getSelectedAnswer',
			]),

			isSelected() {
				return this.getSelectedAnswer(this.questionId) === this.index
			},

			/**
			 * @param  {int} answerIndex
			 * @return {Boolean}
			 */
			isCorrect() {
				return this.answer.is_correct
			},

			showCorrect() {
				return this.isComplete && this.isCorrect
			},

			stats() {
				console.log('stats', this.answer.hits, this.totalHits)
				return _.round(this.answer.hits * 100 / this.totalHits)
			},

			/**
			 * Helper property for debug purposes
			 * @param  {int} answerIndex
			 * @return {Boolean}
			 */
			hintCorrect() {
				return isDebug() && this.isCorrect
			},
		},
	}
</script>
