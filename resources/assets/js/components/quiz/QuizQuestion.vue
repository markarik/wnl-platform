<template>
	<div class="wnl-quiz-question-container">
		<div class="wnl-quiz-question card"
			:class="{
				'is-correct': displayResults && !isUnanswered && isCorrect,
				'is-incorrect': displayResults && !isUnanswered && !isCorrect,
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
							v-if="reactionState"
							:reactableId="question.id"
							:reactableResource="reactableResource"
							:state="reactionState"
							:module="module"
						></wnl-bookmark>
						<a
							:href="'/admin/app/quizes/edit/' + question.id"
							class="question-edit-link"
							v-if="isAdmin"
						>
							<span class="icon is-medium">
								<i class="fa fa-pencil-square-o"></i>
							</span>
						</a>
					</div>
				</div>
			</header>
			<div class="quiz-answers card-content" v-if="!headerOnly">
				<ul>
					<wnl-quiz-answer v-for="(answer, answerIndex) in answers"
						:answer="answer"
						:index="answerIndex"
						:questionId="question.id"
						:totalHits="question.total_hits"
						:key="answerIndex"
						:readOnly="readOnly"
						:isSelected="question.selectedAnswer === answerIndex"
						:answersStats="displayResults && question.answersStats"
						@answerSelected="selectAnswer(answerIndex)"
						@dblclick.native="$emit('answerDoubleclick', {answer: answerIndex})"
					></wnl-quiz-answer>
				</ul>
				<div class="quiz-question-meta">
					<div class="quiz-question-tags">
						<span v-if="displayResults && question.tags">{{$t('questions.question.tags')}}:</span>
						<span v-if="displayResults" v-for="tag, index in question.tags"
							class="quiz-question-tag"
							:key="index"
						>
							#{{tag.name}}
						</span>
					</div>
					<div class="quiz-question-id">#{{question.id}}</div>
				</div>
			</div>
			<div class="card-footer" v-if="!hideComments && ((!headerOnly && displayResults) || showComments)">
				<div class="quiz-question-comments">
					<wnl-comments-list
						commentableResource="quiz_questions"
						urlParam="quiz_question"
						:module="module"
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
	@import 'resources/assets/sass/mixins'

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

		.card-header-icons
			display: flex

		.question-edit-link
			margin-left: 10px
			color: $color-sky-blue

	.card-header-title.is-short-form
		font-size: $font-size-minus-1

	.quiz-header-top
		display: flex
		width: 100%

	.quiz-question-meta
		+flex-space-between()
		color: $color-gray-dimmed
		font-size: $font-size-minus-2
		padding: $margin-small $margin-base 0
		width: 100%

	.wnl-quiz-question
		margin-bottom: $margin-huge

		&.is-correct
			box-shadow: 0 2px 3px $color-correct-shadow, 0 0 0 1px $color-correct-shadow

		&.is-incorrect
			box-shadow: 0 2px 3px $color-incorrect-shadow, 0 0 0 1px $color-incorrect-shadow

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
	import { isNumber } from 'lodash'
	import { mapGetters } from 'vuex'

	import QuizAnswer from 'js/components/quiz/QuizAnswer'
	import CommentsList from 'js/components/comments/CommentsList'
	import Bookmark from 'js/components/global/reactions/Bookmark'

	export default {
		name: 'QuizQuestion',
		components: {
			'wnl-quiz-answer': QuizAnswer,
			'wnl-comments-list': CommentsList,
			'wnl-bookmark': Bookmark,
		},
		props: ['index', 'readOnly', 'headerOnly', 'hideComments', 'showComments', 'question', 'getReaction', 'isQuizComplete', 'module'],
		data() {
			return {
				reactableResource: "quiz_questions"
			}
		},
		computed: {
			...mapGetters(['isMobile', 'isAdmin']),
			...mapGetters('quiz', [
				'getAnswers',
				'isComplete',
				'isResolved',
				'getSelectedAnswer',
			]),
			displayResults() {
				return this.readOnly || this.isComplete || this.isResolved(this.id)
			},
			answers() {
				return this.question.answers
			},
			displayResults() {
				return this.readOnly || this.isQuizComplete || this.question.isResolved
			},
			isCorrect() {
				const selected = this.question.selectedAnswer
				return isNumber(selected) && this.answers[selected].is_correct
			},
			isUnanswered() {
				return !isNumber(this.question.selectedAnswer)
			},
			reactionState() {
				if (typeof this.getReaction === 'function') {
					return this.getReaction(this.reactableResource, this.question.id, "bookmark")
				}
			},
		},
		methods: {
			selectAnswer(answerIndex) {
				const data = {id: this.question.id, answer: answerIndex}
				const eventName = !this.question.isResolved ? 'selectAnswer' : 'resultsClicked'

				this.$emit(eventName, data)
			}
		}
	}
</script>
