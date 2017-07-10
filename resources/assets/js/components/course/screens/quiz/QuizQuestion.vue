<template>
	<div>
		<div class="wnl-quiz-question card margin vertical"
			:class="{
				'is-unresolved': !displayResults,
				'is-unanswered': isUnanswered,
			}">
			<header class="quiz-header card-header">
				<div class="quiz-header-top">
					<div class="card-header-title">
						<div v-html="text"></div>
					</div>
					<div class="card-header-icons">
						<wnl-bookmark
							:reactableId="id"
							:reactableResource="reactableResource"
							:state="reactionState"
							module="quiz"
						></wnl-bookmark>
					</div>
				</div>
			</header>
			<div class="quiz-answers card-content">
				<ul>
					<wnl-quiz-answer v-for="(answer, answerIndex) in answers"
						:answer="answer"
						:index="answerIndex"
						:questionId="id"
						:totalHits="total"
						:key="answerIndex"
						:readOnly="readOnly"
						@answerSelected="selectAnswer(answerIndex)"
					></wnl-quiz-answer>
				</ul>
				<div class="quiz-question-meta">#{{id}}</div>
			</div>
			<div class="card-footer" v-if="displayResults">
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

<style lang="sass" rel="stylesheet/sass">
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

	.wnl-quiz-question
		margin-bottom: $margin-huge

	.quiz-question-comments
		padding: $margin-small $margin-big $margin-big
		width: 100%

	.has-errors .is-unanswered
		color: $color-orange
</style>

<script>
	import { mapGetters, mapActions } from 'vuex'

	import QuizAnswer from 'js/components/course/screens/quiz/QuizAnswer'
	import CommentsList from 'js/components/comments/CommentsList'
	import Bookmark from 'js/components/global/reactions/Bookmark'

	import { swalConfig } from 'js/utils/swal'

	export default {
		name: 'QuizQuestion',
		components: {
			'wnl-quiz-answer': QuizAnswer,
			'wnl-comments-list': CommentsList,
			'wnl-bookmark': Bookmark,
		},
		data() {
			return {
				reactableResource: "quiz_questions"
			}
		},
		props: ['id', 'index', 'text', 'total', 'readOnly'],
		computed: {
			...mapGetters('quiz', [
				'getAnswers',
				'isComplete',
				'isResolved',
				'getSelectedAnswer',
				'getReaction'
			]),
			displayResults() {
				return this.readOnly || this.isComplete || this.isResolved(this.id)
			},
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
			reactionState() {
				return this.getReaction(this.reactableResource, this.id, "bookmark")
			}
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
			}
		}
	}
</script>
