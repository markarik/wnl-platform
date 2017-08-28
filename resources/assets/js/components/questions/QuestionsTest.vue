<template>
	<div>
		<div class="questions-test-header-container">
			<div class="questions-test-header" :class="{'is-sticky': hasStickyHeader}" ref="header">
				<div v-if="!isComplete" class="in-progress">
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
				<div v-else class="complete">
					TEST ROZWIAZANY!!!!
					<button @click="$emit('endTest')">Wroć do bazy pytań</button>
				</div>
				<progress class="progress is-success" :max="totalCount" :value="answeredCount">
					{{answeredCount}}
				</progress>
			</div>
		</div>

		<wnl-quiz-list
			module="questions"
			:allQuestions="questions"
			:getReaction="getReaction"
			:isComplete="isComplete"
			:isProcessing="false"
			:plainList="true"
			@selectAnswer="onSelectAnswer"
			@checkQuiz="$emit('checkQuiz')"
		/>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	$header-height: 40px

	.questions-test-header-container
		height: $header-height + 2 * $margin-base

	.questions-test-header
		height: $header-height
		padding: $margin-base 0

		&.is-sticky
			background: $color-white-overlay
			left: 0
			height: $header-height + 2 * $margin-small
			padding: $margin-small $margin-base
			position: absolute
			right: 0
			top: -25px
			transition: top $transition-length-base
			z-index: $z-index-navbar - 1

			&:hover
				top: 0
				transition: top $transition-length-base

		.in-progress
			display: flex
			font-size: $font-size-minus-1
			justify-content: space-between

			.answered
				text-transform: uppercase

	.progress
		height: 2px
		margin-top: $margin-small
</style>

<script>
	import {debounce, isEmpty} from 'lodash'

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
				headerOffset: 0,
				scrollableContainer: {},
			}
		},
		computed: {
			answeredCount() {
				return this.answeredQuestions.length
			},
			answeredQuestions() {
				return this.questions.filter(question => {
					return [null, false].indexOf(question.selectedAnswer) === -1
				})
			},
			hasStickyHeader() {
				return this.currentScroll > this.headerOffset
			},
			isComplete() {
				return !isEmpty(this.testResults)
			},
			totalCount() {
				return this.questions.length
			},
		},
		methods: {
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
