<template>
	<div>
		<div class="wnl-quiz-question card margin vertical"
			:class="{
				'is-unresolved': !displayResults,
				'is-unanswered': isUnanswered,
				'is-mobile': isMobile,
			}">
			<header class="quiz-header card-header">
				<div class="quiz-header-top">
					<div class="card-header-title" :class="{'clickable': headerOnly, 'is-short-form': headerOnly}" @click="$emit('headerClicked')">
						<div v-html="question.text"></div>
					</div>
					<div class="card-header-icons">
						<wnl-bookmark
							:reactableId="question.id"
							:reactableResource="reactableResource"
							:state="reactionState"
							module="quiz"
						></wnl-bookmark>
					</div>
				</div>
			</header>
			<div class="quiz-answers card-content" v-if="!headerOnly">
				<ul>
					<wnl-quiz-answer v-for="(answer, answerIndex) in question.answers"
						:answer="answer"
						:index="answerIndex"
						:questionId="question.id"
						:totalHits="question.total_hits"
						:key="answerIndex"
						:readOnly="readOnly"
						@answerSelected="selectAnswer(answerIndex)"
					></wnl-quiz-answer>
				</ul>
				<div class="quiz-question-meta">#{{question.id}}</div>
			</div>
			<div class="card-footer" v-if="(!headerOnly && displayResults) || showComments">
				<div class="quiz-question-comments">
					<wnl-comments-list
						module="quiz"
						commentableResource="quiz_questions"
						urlParam="quiz_question"
						:commentableId="question.id"
						:isUnique="showComments">
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

	.card-header-title.is-short-form
		font-size: $font-size-minus-1

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

		&.is-mobile

			.quiz-header,
			.quiz-answers
				padding: $margin-small

				.card-header-title,
				.card-header-icons
					padding: $margin-small

			.quiz-header
				font-size: $font-size-minus-1

			.quiz-answer
				font-size: $font-size-minus-1

	.quiz-question-comments
		padding: $margin-small $margin-big $margin-base
		width: 100%

	.has-errors .is-unanswered
		color: $color-orange
</style>

<script>
	import { mapGetters, mapActions } from 'vuex'

	import QuizAnswer from 'js/components/quiz/QuizAnswer'
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
		props: ['index', 'readOnly', 'headerOnly', 'showComments', 'question'],
		data() {
			return {
				reactableResource: "quiz_questions"
			}
		},
		computed: {
			...mapGetters(['isMobile']),
			...mapGetters('quiz', [
				'isComplete',
				'isResolved',
				'getSelectedAnswer',
				'getReaction'
			]),
			displayResults() {
				return this.readOnly || this.isComplete || this.isResolved(this.question.id)
			},
			selectedAnswer() {
				return this.getSelectedAnswer(this.question.id)
			},
			isUnanswered() {
				return this.selectedAnswer === null
			},
			reactionState() {
				return this.getReaction(this.reactableResource, this.question.id, "bookmark")
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
						id: this.question.id,
						answer: answerIndex
					})
				}
			}
		}
	}
</script>
