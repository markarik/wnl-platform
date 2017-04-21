<template>
	<div class="card margin vertical">
		<header class="card-header">
			<p class="card-header-title">{{text}}</p>
		</header>
		<div class="card-content">
			<transition-group name="flip-list" tag="ul">
				<li class="quiz-answer" v-for="(answer, answerIndex) in answers"
					:class="{'is-selected': isSelected(answerIndex), 'is-correct': isCorrect(answerIndex)}"
					:key="answer"
					@click="selectAnswer(answerIndex)"
				>
					{{answer.text}}
				</li>
			</transition-group>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.quiz-answer
		border-bottom: $border-light-gray
		cursor: pointer
		list-style-position: inside
		list-style-type: upper-alpha
		padding: $margin-base
		margin: 0

		&:last-child
			border: 0

		&:hover
			background: $color-light-gray

		&:active
			background: $color-inactive-gray

		&, &:hover, &:active
			transition: all $transition-length-base

	.is-correct
		background: $color-green

		&:active, &:hover
			background: $color-green

	.is-selected
		background: $color-ocean-blue
		color: $color-white

		&:active, &:hover
			background: $color-ocean-blue
			color: $color-white
</style>

<script>
	import * as types from 'js/store/mutations-types'
	import { mapGetters, mapMutations } from 'vuex'

	export default {
		props: ['answers', 'index', 'text', 'total', 'isResolved'],
		name: 'QuizQuestion',
		data() {
			return {
				selected: null,
			}
		},
		computed: {
			...mapGetters('quiz', [
				'isComplete',
				'getSelectedAnswer',
			]),
			number() {
				return this.index + 1
			},
		},
		methods: {
			...mapMutations('quiz', [
				types.QUIZ_SELECT_ANSWER,
			]),

			/**
			 * @param  {int} answerIndex
			 * @return {Boolean}
			 */
			isSelected(answerIndex) {
				return this.getSelectedAnswer(this.index) === answerIndex
			},

			/**
			 * @param  {int} answerIndex
			 * @return {Boolean}
			 */
			isCorrect(answerIndex) {
				return this.isComplete && this.answers[answerIndex].is_correct
			},

			/**
			 * Commits a Vuex mutatation that sets a selectedAnswer for the
			 * current question.
			 * @param  {int} answerIndex Index of a selected answer
			 * @return {void}
			 */
			selectAnswer(answerIndex) {
				this[types.QUIZ_SELECT_ANSWER]({
					index: this.index,
					answer: answerIndex
				})
			}
		}
	}
</script>
