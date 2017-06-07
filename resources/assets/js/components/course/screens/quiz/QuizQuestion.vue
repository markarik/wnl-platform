<template>
	<div>
		<div class="wnl-quiz-question card margin vertical"
			:class="{
				'is-unresolved': !isResolved(id),
				'is-unanswered': isUnanswered,
			}">
			<header class="quiz-header card-header">
				<div class="quiz-header-top">
					<div class="card-header-title">
						<div v-html="text"></div>
					</div>
					<div class="card-header-icons">
						<a class="quiz-question-icon" @click="mockSaving" title="Zapisz to pytanie">
							<span class="icon is-small">
								<i class="fa fa-bookmark-o"></i>
							</span>
							Zapisz
						</a>
					</div>
				</div>
			</header>
			<div class="quiz-answers card-content">
				<transition-group name="flip-list" tag="ul">
					<wnl-quiz-answer v-for="(answer, answerIndex) in answers"
						:answer="answer"
						:index="answerIndex"
						:questionId="id"
						:totalHits="total"
						:key="answerIndex"
						@answerSelected="selectAnswer(answerIndex)"
					></wnl-quiz-answer>
				</transition-group>
				<div class="quiz-question-meta">#{{id}}</div>
			</div>
			<div class="card-footer" v-if="isComplete">
				<div class="quiz-question-comments">
					<wnl-comments-list
						module="quiz"
						commentableResource="quiz_questions"
						:commentableId="id">
					</wnl-comments-list>
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

	.quiz-header
		align-items: flex-start
		flex-direction: column

	.quiz-header-top
		display: flex
		width: 100%

	.quiz-question-meta
		color: $color-gray-dimmed
		font-size: $font-size-minus-2
		padding: $margin-small $margin-base 0
		text-align: right
		width: 100%

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
		width: 100%


	.has-errors .is-unanswered
		color: $color-orange
</style>

<script>
	import { mapGetters, mapActions } from 'vuex'

	import QuizAnswer from 'js/components/course/screens/quiz/QuizAnswer'
	import CommentsList from 'js/components/comments/CommentsList.vue'

	import { swalConfig } from 'js/utils/swal'

	export default {
		name: 'QuizQuestion',
		components: {
			'wnl-quiz-answer': QuizAnswer,
			'wnl-comments-list': CommentsList,
		},
		props: ['id', 'index', 'text', 'total'],
		computed: {
			...mapGetters('quiz', [
				'getAnswers',
				'isComplete',
				'isResolved',
				'getSelectedAnswer',
			]),
			answers() {
				return this.getAnswers(this.id)
			},
			selectedAnswer() {
				return this.getSelectedAnswer(this.id)
			},
			isUnanswered() {
				return this.selectedAnswer === null
			},
			/**
			 * @return {Boolean}
			 */
			hasComments() {
				// return this.comments.length > 0
			},
			/**
			 * @return {Boolean}
			 */
			showComments() {
				// return this.isComplete && this.hasComments
			},
		},
		methods: {
			...mapActions('quiz', ['commitSelectAnswer']),

			/**
			 * Commits a Vuex mutatation that sets a selectedAnswer for the
			 * current question.
			 * @param  {int} answerIndex Index of a selected answer
			 * @return {void}
			 */
			selectAnswer(answerIndex) {
				if (!this.isComplete) {
					this.commitSelectAnswer({
						id: this.id,
						answer: answerIndex
					})
				}
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
