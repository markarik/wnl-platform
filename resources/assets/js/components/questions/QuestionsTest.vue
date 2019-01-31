<template>
	<div>
		<div class="questions-test-header-container" :class="{'is-mobile': isMobile, 'is-complete': isComplete}">
			<div class="questions-test-header" :class="{'is-sticky': hasStickyHeader}" ref="header">
				<div v-if="!isComplete">
					<div class="in-progress">
						<span>
							<span class="answered" v-if="!isMobile || unansweredCount === 0">
								{{$t('questions.solving.test.headers.answered', {
									answered: answeredCount,
									total: totalCount,
								})}}
							</span>
							<a v-show="unansweredCount > 0" class="toggle-unanswered"
								@click="filterUnanswered = !filterUnanswered">
								{{unansweredToggleMessage}}
								({{unansweredToggleCount}})
							</a>
						</span>
						<wnl-quiz-timer ref="timer"
							:hideTime="hideTime"
							:time="time"
							@clicked="hideTime = !hideTime"
							@timesUp="onTimesUp"/>
					</div>
					<progress class="progress is-success" :max="totalCount" :value="answeredCount">
						{{answeredCount}}
					</progress>
					<div class="test-controls">
						<a class="is-small is-primary is-outlined" :class="{'button': !isMobile}" @click="checkQuiz">
							{{$t('questions.solving.resolve')}}
						</a>
						<a class="is-small" :class="{'button': !isMobile}" @click="$emit('endQuiz')">
							{{$t('questions.solving.abort')}}
						</a>
					</div>
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
						<span v-for="(questions, status) in testResults"
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

		<div v-if="lastPage > 1" class="pagination top">
			<wnl-pagination
				ref="firstpagination"
				:currentPage="currentPage"
				:lastPage="lastPage"
				@changePage="changePage"
			/>
		</div>

		<wnl-quiz-list
			module="questions"
			ref="quizlist"
			:allQuestions="questionsCurrentPage"
			:getReaction="getReaction"
			:isComplete="isComplete"
			:isProcessing="testProcessing"
			:plainList="true"
			@selectAnswer="onSelectAnswer"
			@userEvent="onUserEvent"
		>
			<div slot="question-number" slot-scope="quizList">
				{{((currentPage - 1) * perPage) + quizList.index + 1}}/{{questions.length}}
			</div>
		</wnl-quiz-list>

		<div v-if="lastPage > 1" class="pagination bottom">
			<wnl-pagination
				:currentPage="currentPage"
				:lastPage="lastPage"
				@changePage="changePage"
			/>
		</div>

		<p v-if="!isComplete" class="questions-test-resolve">
			<a class="button is-outlined is-small is-primary" :class="{'is-loading': testProcessing}" @click="checkQuiz">
				{{$t('questions.solving.resolve')}}
			</a>
		</p>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

	$header-height: 60px

	.questions-test-header-container
		height: $header-height + 2 * $margin-base

		&.is-mobile

			.is-small
				font-size: $font-size-minus-1

			&.is-complete
				height: $header-height * 2

				.questions-test-header
					height: $header-height * 2

	.pagination
		+flex-center()

		&.top
			margin: $margin-base 0

		&.bottom
			margin-bottom: $margin-big

	.questions-test-header
		height: $header-height + 2 * $margin-base
		padding: $margin-base 0
		top: -50px
		transition: top $transition-length-base

		&.is-sticky
			+shadow()

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
				margin-right: $margin-small
				text-transform: uppercase

			.toggle-unanswered
				font-size: $font-size-minus-2
				white-space: nowrap

			.timer
				+clickable()
				white-space: nowrap

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
		margin: $margin-small 0

	.test-controls
		+flex-space-between()

	.questions-test-resolve
		+flex-center()
		margin-bottom: $margin-huge
</style>

<script>
import {mapGetters} from 'vuex';
import {debounce, isEmpty, isNumber, size} from 'lodash';

import QuizList from 'js/components/quiz/QuizList';
import QuizTimer from 'js/components/quiz/QuizTimer';
import Pagination from 'js/components/global/Pagination';
import emits_events from 'js/mixins/emits-events';
import features from 'js/consts/events_map/features.json';
import context from 'js/consts/events_map/context.json';

import {scrollToElement} from 'js/utils/animations';

export default {
	name: 'QuestionsTest',
	components: {
		'wnl-quiz-list': QuizList,
		'wnl-quiz-timer': QuizTimer,
		'wnl-pagination': Pagination,
	},
	mixins: [emits_events],
	props: [
		'getReaction',
		'questions',
		'onCheckQuiz',
		'onSelectAnswer',
		'testProcessing',
		'testResults',
		'time',
	],
	data() {
		return {
			canShowStickyHeader: true,
			currentPage: 1,
			currentScroll: 0,
			filterResults: false,
			filterUnanswered: false,
			headerOffset: 0,
			hideTime: false,
			perPage: 30,
			scrollableContainer: {},
		};
	},
	computed: {
		...mapGetters(['isMobile']),
		answeredCount() {
			return this.totalCount - this.unansweredCount;
		},
		correctCount() {
			return this.testResults && size(this.testResults.correct);
		},
		filteredQuestions() {
			if (!this.isComplete) {
				return this.filterUnanswered && this.unansweredCount > 0
					? this.unansweredQuestions
					: this.questions;
			}

			return isEmpty(this.filterResults)
				? this.questions
				: this.testResults[this.filterResults];
		},
		hasStickyHeader() {
			return this.canShowStickyHeader &&
					this.currentScroll > this.headerOffset + 100;
		},
		isComplete() {
			return !isEmpty(this.testResults);
		},
		lastPage() {
			return Math.ceil(size(this.filteredQuestions) / this.perPage);
		},
		questionsCurrentPage() {
			let c = this.currentPage, p = this.perPage;
			return this.filteredQuestions.slice((c - 1) * p, c * p);
		},
		score() {
			return this.testResults &&
					Math.floor(this.correctCount * 100 / this.totalCount);
		},
		totalCount() {
			return this.questions.length;
		},
		unansweredCount() {
			return this.unansweredQuestions.length;
		},
		unansweredQuestions() {
			return this.questions.filter(question => !isNumber(question.selectedAnswer));
		},
		unansweredToggleCount() {
			return this.filterUnanswered ? this.totalCount : this.unansweredCount;
		},
		unansweredToggleMessage() {
			return this.filterUnanswered
				? this.$t('questions.solving.unanswered.all')
				: this.$t('questions.solving.unanswered.filter');
		},
	},
	methods: {
		changePage(n) {
			this.currentPage = n;
			if (this.currentScroll > 200) {
				scrollToElement(this.$refs.firstpagination.$el);
			}
		},
		checkQuiz() {
			this.$emit('checkQuiz', {unansweredCount: this.unansweredCount});
			if (this.unansweredCount > 0) {
				this.$refs.quizlist.scrollToFirstUnanswered();
			}
		},
		toggleFilter(status) {
			this.filterResults = this.filterResults === status ? '' : status;
		},
		onScroll: debounce(function({target: {scrollTop}}) {
			if (this.isMobile) {
				this.canShowStickyHeader = scrollTop < this.currentScroll;
			}

			return this.currentScroll = scrollTop;
		}, 50),
		onTimesUp() {
			this.$refs.timer.stopTimer();
			this.checkQuiz();
		},
		onUserEvent(payload) {
			this.emitUserEvent({
				feature: features.quiz_questions.value,
				subcontext: context.questions_bank.subcontext.test_yourself.value,
				...payload,
			});
		}
	},
	mounted() {
		!this.isComplete && this.$refs.timer.startTimer();
		this.headerOffset = this.$refs.header.offsetTop;

		// TODO: Pass class name as props
		this.scrollableContainer = document.getElementsByClassName('scrollable-main-container')[0];
		this.scrollableContainer.addEventListener('scroll', this.onScroll);
		this.$emit('testStart');
	},
	beforeDestroy() {
		this.scrollableContainer.removeEventListener('scroll', this.onScroll);
	},
	watch: {
		hasStickyHeader(to, from) {
			this.hideTime = to;
		},
		lastPage(to, from) {
			if (to < this.currentPage) this.currentPage = to;
		},
	}
};
</script>
