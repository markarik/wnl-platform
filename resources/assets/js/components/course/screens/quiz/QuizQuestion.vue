<template>
	<div>
		<div class="wnl-quiz-question card margin vertical"
			:class="{'is-unresolved': !isResolved(this.index)}">
			<header class="quiz-header card-header">
				<p class="card-header-title" v-html="text"></p>
				<div class="card-header-icons">
					<a class="quiz-question-icon" @click="mockSaving" title="Zapisz to pytanie">
						<span class="icon is-small">
							<i class="fa fa-bookmark-o"></i>
						</span>
						Zapisz
					</a>
				</div>
			</header>
			<div class="quiz-answers card-content">
				<transition-group name="flip-list" tag="ul">
					<li class="quiz-answer" v-for="(answer, answerIndex) in answers"
						:class="{
							'is-selected': isSelected(answerIndex),
							'is-correct': isCorrect(answerIndex),
							'is-hinted': hintCorrect(answerIndex),
						}"
						:key="answer"
						@click="selectAnswer(answerIndex)"
					>
						<div class="quiz-answer-content">
							{{answer.text}}
						</div>
						<div class="quiz-answer-stats" v-if="isComplete">
							<span class="tag" :title="`${answer.stats}% osób wybrało tę odpowiedź`">
								{{answer.stats}}%
							</span>
						</div>
					</li>
				</transition-group>
			</div>
			<div class="card-footer" v-if="showComments">
				<div class="quiz-question-comments" v-if="hasComments">
					<wnl-comments-list :comments="comments"></wnl-comments-list>
				</div>
			</div>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.card-content ul
		counter-reset: list

	.quiz-question-icon
		display: block
		font-size: $font-size-minus-3
		padding: $margin-tiny 0
		text-align: center
		text-transform: uppercase

		.icon
			display: block
			margin: 0 auto -0.2em

	.quiz-header,
	.quiz-answers
		padding: $margin-base

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

		&:last-child
			border: 0

		&.is-hinted
			&::after
				content: '☑'
				position: absolute
				right: $margin-base

	.wnl-quiz-question
		margin-bottom: $margin-huge

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

	.quiz-question-comments
		padding: $margin-big
</style>

<script>
	import * as types from 'js/store/mutations-types'
	import CommentsList from 'js/components/comments/CommentsList.vue'
	import { mapGetters, mapMutations } from 'vuex'
	import { isDebug } from 'js/utils/env'
	import { swalConfig } from 'js/utils/swal'

	export default {
		name: 'QuizQuestion',
		components: {
			'wnl-comments-list': CommentsList,
		},
		props: ['answers', 'comments', 'index', 'text', 'total'],
		computed: {
			...mapGetters('quiz', [
				'isComplete',
				'isResolved',
				'getSelectedAnswer',
			]),
			number() {
				return this.index + 1
			},
			/**
			 * @return {Boolean}
			 */
			hasComments() {
				return this.comments.length > 0
			},
			/**
			 * @return {Boolean}
			 */
			showComments() {
				return this.isComplete && this.hasComments
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
			isCorrect(answerIndex) {
				return this.isComplete && this.answers[answerIndex].is_correct
			},

			/**
			 * Helper property for debug purposes
			 * @param  {int} answerIndex
			 * @return {Boolean}
			 */
			hintCorrect(answerIndex) {
				return isDebug() &&
					!this.isComplete &&
					this.answers[answerIndex].is_correct
			},

			/**
			 * Commits a Vuex mutatation that sets a selectedAnswer for the
			 * current question.
			 * @param  {int} answerIndex Index of a selected answer
			 * @return {void}
			 */
			selectAnswer(answerIndex) {
				if (!this.isComplete) {
					this[types.QUIZ_SELECT_ANSWER]({
						index: this.index,
						answer: answerIndex
					})
				}
			},

			/**
			 * @param  {int} answerIndex
			 * @return {Boolean}
			 */
			isSelected(answerIndex) {
				return this.getSelectedAnswer(this.index) === answerIndex
			},

			/**
			 * A temporary method, to be removed when Collections are done.
			 * The method displays a modal informing about the upcoming feature.
			 */
			mockSaving() {
				this.$swal(swalConfig({
					html: `<p class="normal">Pracujemy nad zapisywaniem pytań do własnej Kolekcji!</p>
						<p class="normal" style="margin-top: 0.6em">Dzięki tej funkcji, będziecie mogli zachowywać wybrane pytania i wracać do nich w dowolnym momencie!</p>`,
					title: 'Już wkrótce!',
					type: 'info',
				}))
			}
		}
	}
</script>
