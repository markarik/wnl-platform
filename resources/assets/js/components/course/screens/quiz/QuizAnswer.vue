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
	@import 'resources/assets/sass/variables'

	.wnl-quiz-question.is-unresolved
		.quiz-answer
			cursor: pointer

			&:hover
				background: $color-light-gray

			&:active
				background: $color-inactive-gray

			&, &:hover, &:active
				transition: all $transition-length-base

			&.is-selected
				background: $color-ocean-blue
				color: $color-white

				&:active, &:hover
					background: $color-ocean-blue
					color: $color-white

	.quiz-answer
		display: flex
		border-bottom: $border-light-gray
		justify-content: space-between
		list-style-type: none
		padding: $margin-base $margin-base $margin-base $margin-huge
		position: relative
		margin: 0

		&::before
			content: counter(list, upper-alpha) ")"
			counter-increment: list
			left: $margin-base
			position: absolute

		&.is-hinted
			&::after
				content: '☑'
				position: absolute
				right: $margin-base

	.quiz-answer.is-correct
		background: $color-green
		color: $color-white

		&:active, &:hover
			background: $color-green
			color: $color-white

			.quiz-answer-content
				flex: 1 1 auto

	.quiz-answer-content
		flex: 1 1 auto

	.quiz-answer-stats
		align-self: center
		cursor: help
		flex: 0 0 auto
		margin-left: $margin-base
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
