<template>
	<div>
		<div class="questions-test-header-container">
			<div class="questions-test-header" :class="{'is-sticky': hasStickyHeader}" ref="header">
				<div v-if="!isComplete">
					<div class="in-progress">
						<span class="answered">
							{{$t('questions.solving.test.headers.answered', {
								answered: answeredCount,
								total: totalCount,
							})}}
						</span>
						<wnl-quiz-timer ref="timer"
							:time="time"
							@timesUp="$emit('checkQuiz')"/>
					</div>
					<progress class="progress is-success" :max="totalCount" :value="answeredCount">
						{{answeredCount}}
					</progress>
				</div>
				<div v-else class="complete">
					<div class="end-quiz">
						<span class="result">
							{{$t('questions.solving.score')}}
							<span class="percent">{{score}}%</span>
							<span class="score">
								(<span class="correct">{{correctCount}}</span>/{{totalCount}})
							</span>
						</span>
						<a class="button is-small is-outlined" @click="$emit('endQuiz')">
							{{$t('questions.solving.new')}}
						</a>
					</div>
					<div class="results">
						<span class="results-heading">
							{{$t('questions.solving.results.displayOnly')}}
						</span>
						<span v-for="questions, status in testResults"
							v-if="questions.length > 0"
							:class="[{'is-active': filterResults === status}, `results-${status}`]"
							:key="status"
							@click="toggleFilter(status)"
						>
							{{$t(`questions.solving.results.${status}`)}} ({{questions.length}})
						</span>
					</div>
				</div>
			</div>
		</div>

		<wnl-quiz-list
			module="questions"
			:allQuestions="filteredQuestions"
			:getReaction="getReaction"
			:isComplete="isComplete"
			:isProcessing="false"
			:plainList="true"
			@selectAnswer="onSelectAnswer"
			@checkQuiz="$emit('checkQuiz', {unansweredCount})"
		/>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

	$header-height: 60px

	.questions-test-header-container
		height: $header-height + 2 * $margin-base

	.questions-test-header
		height: $header-height + 2 * $margin-base
		padding: $margin-base 0

		&.is-sticky
			background: $color-white-overlay
			left: 0
			height: $header-height + 2 * $margin-small
			padding: $margin-small $margin-base
			position: absolute
			right: 0
			top: 0
			transition: top $transition-length-base
			z-index: $z-index-navbar - 1

		.in-progress
			display: flex
			font-size: $font-size-minus-1
			justify-content: space-between

			.answered
				text-transform: uppercase

		.complete

			.end-quiz
				+flex-space-between()
				margin-bottom: $margin-base

				.result
					letter-spacing: 1px
					text-transform: uppercase

					.correct
						color: $color-green
						font-weight: $font-weight-bold

					.percent
						font-size: $font-size-plus-2
						font-weight: $font-weight-bold

					.score
						font-size: $font-size-minus-1

			.results
				font-size: $font-size-minus-1

				span
					+clickable()
					margin-right: $margin-small

					&.is-active
						font-weight: $font-weight-bold
						text-decoration: underline

				.results-correct
					color: $color-green

				.results-incorrect
					color: $color-red

				.results-unanswered
					color: $color-background-gray

			.results-heading
				color: $color-background-gray

	.progress
		height: 2px
		margin-top: $margin-small
</style>

<script>
	import {debounce, isEmpty, isNumber, size} from 'lodash'

	import QuizList from 'js/components/quiz/QuizList'
	import QuizTimer from 'js/components/quiz/QuizTimer'

	export default {
		name: 'QuestionsTest',
		components: {
			'wnl-quiz-list': QuizList,
			'wnl-quiz-timer': QuizTimer
		},
		props: ['questions', 'time', 'onSelectAnswer', 'onCheckQuiz', 'getReaction', 'testResults'],
		data() {
			return {
				currentScroll: 0,
				filterResults: false,
				headerOffset: 0,
				scrollableContainer: {},
			}
		},
		computed: {
			answeredCount() {
				return this.answeredQuestions.length
			},
			answeredQuestions() {
				return this.questions.filter(question => isNumber(question.selectedAnswer))
			},
			correctCount() {
				return this.testResults && size(this.testResults.correct)
			},
			filteredQuestions() {
				if (!this.isComplete) return this.questions

				return isEmpty(this.filterResults)
					? this.questions
					: this.testResults[this.filterResults]
			},
			hasStickyHeader() {
				return this.currentScroll > this.headerOffset
			},
			isComplete() {
				return !isEmpty(this.testResults)
			},
			score() {
				return this.testResults &&
					Math.floor(this.correctCount * 100 / this.totalCount)
			},
			totalCount() {
				return this.questions.length
			},
			unansweredCount() {
				return this.totalCount - this.answeredCount
			},
		},
		methods: {
			toggleFilter(status) {
				this.filterResults = this.filterResults === status ? '' : status
			},
			onScroll: debounce(function({target: {scrollTop}}) {
				this.currentScroll = scrollTop
			}, 50),
		},
		mounted() {
			!this.isComplete && this.$refs.timer.startTimer()
			this.headerOffset = this.$refs.header.offsetTop

			// TODO: Pass class name as props
			this.scrollableContainer = document.getElementsByClassName('scrollable-main-container')[0]
			this.scrollableContainer.addEventListener('scroll', this.onScroll)
		},
		beforeDestroy() {
			this.scrollableContainer.removeEventListener('scroll', this.onScroll)
		},
	}
</script>
