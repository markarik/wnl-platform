<template>
	<li
		class="quiz-answer"
		:class="{
			'is-selected': isSelected && !showCorrect,
			'is-correct': showCorrect,
			'is-hinted': hintCorrect,
			'is-mobile': isMobile,
		}"
		@click.prevent="$emit('answerSelected')"
	>
		<div class="quiz-answer-content">
			{{answer.text}}
		</div>
		<div v-if="isNumber(stats)" class="quiz-answer-stats">
			<span class="tag" :title="`${stats}% osób wybrało tę odpowiedź`">
				{{stats}}%
			</span>
		</div>
	</li>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

	.wnl-quiz-question.is-unresolved
		.quiz-answer
			cursor: pointer
			user-select: none

			&.is-selected
				background: $color-ocean-blue

				&:active, &:hover
					background: $color-ocean-blue
					color: $color-white

			&:hover
				background: $color-light-gray

			&:active
				background: $color-inactive-gray

			&, &:hover, &:active
				transition: all $transition-length-base

	.wnl-quiz-question
		.quiz-answer
			&.is-selected
				background: $color-red
				color: $color-white

	.quiz-answer
		display: flex
		border-bottom: $border-light-gray
		justify-content: space-between
		line-height: $line-height-minus
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

		&.is-mobile
			padding: $margin-base $margin-small $margin-base $margin-big + $margin-tiny

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
import { mapGetters } from 'vuex';
import { isFinite } from 'lodash';

import { isDebug } from 'js/utils/env';

export default {
	name: 'QuizAnswer',
	props: ['answer', 'index', 'questionId', 'totalHits', 'readOnly', 'isSelected', 'answersStats'],
	computed: {
		...mapGetters(['isMobile']),
		/**
			 * @param  {int} answerIndex
			 * @return {Boolean}
			 */
		isCorrect() {
			return this.answer.is_correct;
		},
		showCorrect() {
			return this.isCorrect && this.$parent.displayResults;
		},
		stats() {
			if (!this.answer.hasOwnProperty('stats')) return false;

			return this.answer.stats;
		},
		/**
			 * Helper property for debug purposes
			 * @param  {int} answerIndex
			 * @return {Boolean}
			 */
		hintCorrect() {
			return isDebug() && this.isCorrect;
		},
	},
	methods: {
		isNumber(n) {
			return isFinite(n);
		}
	}
};
</script>
