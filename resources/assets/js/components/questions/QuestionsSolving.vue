<template>
	<div>
		<div class="tabs" v-if="!testMode">
			<ul>
				<li
					v-for="view in views"
					:class="{'is-active': view.name === activeView}"
					@click="activeView = view.name"
				>
					<a>
						<span class="icon is-small"><i class="fa" :class="view.icon"></i></span>
						{{$t(`questions.solving.tabs.${view.name}`, {
							count: questionsListCount,
							current: questionNumber(currentQuestion.index)
						})}}
					</a>
				</li>
			</ul>
		</div>
		<div class="questions-list-info" v-if="!testMode">
			<div class="active-filters">
				{{activeFiltersForDisplay}}
			</div>
			<a v-if="activeView === 'list'"
				class="button is-small is-outlined"
				@click="showListResults = !showListResults"
			>
				{{toggleAnswersMessage}}
			</a>
		</div>

		<div v-if="hasCurrentQuestion">
			<!-- Current Question -->
			<div v-if="activeView === 'current'">
				<wnl-active-question
					:module="module"
					:getReaction="getReaction"
					:question="currentQuestion"
					:questionNumber="currentQuestionNumber"
					:allQuestionsCount="questionsListCount"
					@changeQuestion="changeQuestion"
					@selectAnswer="selectAnswer"
					@verify="onVerify"
				/>
			</div>

			<!-- List -->
			<div v-if="activeView === 'list'" class="questions-list">
				<div class="pagination-container">
					<wnl-pagination v-if="meta.lastPage && meta.lastPage > 1"
						:currentPage="meta.currentPage"
						:lastPage="meta.lastPage"
						@changePage="changePage"
					/>
				</div>

				<div v-if="questionsCurrentPage.length > 0"
					v-for="(question, index) in questionsCurrentPage"
					class="questions-list-item"
					:key="index"
				>
					<div class="questions-list-numbering">
						<span class="matched-count">
							{{ $t('questions.solving.withNumber', {number: questionNumber(index)}) }}/{{questionsListCount}}
						</span>
						<span class="question-id">#{{question.id}}</span>
					</div>
					<wnl-quiz-question
						:class="`quiz-question-${question.id}`"
						:getReaction="getReaction"
						:headerOnly="!showListResults"
						:id="question.id"
						:module="module"
						:question="question"
						:readOnly="showListResults"
						:hideComments="true"
						@headerClicked="setQuestion(index)"
					/>
				</div>

				<div v-if="questionsCurrentPage.length > 5" class="pagination-container">
					<wnl-pagination v-if="meta.lastPage && meta.lastPage > 1"
						:currentPage="meta.currentPage"
						:lastPage="meta.lastPage"
						@changePage="changePage"
					/>
				</div>
			</div>

			<!-- Test -->
			<div v-if="activeView === 'test'">
				<wnl-questions-test-builder
					:getReaction="getReaction"
					:questions="testQuestions"
					:questionsPoolSize="questionsListCount"
					:testMode="testMode"
					:testProcessing="testProcessing"
					:testResults="testResults"
					@buildTest="buildTest"
					@selectAnswer="selectAnswer"
					@checkQuiz="(payload) => $emit('checkQuiz', payload)"
					@endQuiz="$emit('endQuiz')"
				/>
			</div>
		</div>

		<div v-else-if="!loading" class="has-text-centered margin vertical metadata">
			{{$t('questions.zeroState')}}
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.tabs
		font-size: $font-size-minus-1

		.is-active
			font-weight: $font-weight-regular

	.questions-list-info
		align-items: flex-start
		display: flex
		justify-content: space-between
		margin: -$margin-base 0 0

		.active-filters
			font-size: $font-size-minus-2
			font-style: italic
			color: $color-background-gray

	.pagination-container
		display: flex
		justify-content: center
		margin: $margin-base 0
		width: 100%

	.questions-list-item
		.questions-list-numbering
			color: $color-background-gray
			display: flex
			justify-content: space-between
			font-size: $font-size-minus-2
			line-height: $line-height-minus
			margin-bottom: $margin-small

			.matched-count
				font-weight: bold

			.question-id
				font-weight: $font-weight-regular

		.wnl-quiz-question-container
			margin-bottom: -$margin-base
			width: 100%

			.wnl-quiz-question
				margin: 0
</style>

<script>
	import {isEmpty} from 'lodash'

	import ActiveQuestion from 'js/components/questions/ActiveQuestion'
	import QuestionsTestBuilder from 'js/components/questions/QuestionsTestBuilder'
	import QuizQuestion from 'js/components/quiz/QuizQuestion'
	import Pagination from 'js/components/global/Pagination'

	const views = [
		{
			name: 'current',
			icon: 'fa-check'
		},
		{
			name: 'list',
			icon: 'fa-list'
		},
		{
			name: 'test',
			icon: 'fa-clock-o'
		},
	]

	const limit = 30

	export default {
		name: 'QuestionsSolving',
		components: {
			'wnl-active-question': ActiveQuestion,
			'wnl-questions-test-builder': QuestionsTestBuilder,
			'wnl-quiz-question': QuizQuestion,
			'wnl-pagination': Pagination,
		},
		props: {
			activeFilters: {
				default: () => [],
				type: Array,
			},
			currentQuestion: {
				default: () => {},
				type: Object,
			},
			getReaction: {
				default: () => {},
				type: Function,
			},
			loading: {
				default: false,
				type: Boolean,
			},
			meta: {
				default: () => {},
				type: Object,
			},
			module: {
				default: 'questions',
				type: String,
			},
			questions: {
				default: () => [],
				type: Array,
			},
			questionsCurrentPage: {
				default: () => [],
				type: Array,
			},
			questionsListCount: {
				default: 0,
				type: Number,
			},
			testMode: {
				default: false,
				type: Boolean,
			},
			testQuestions: {
				default: () => [],
				type: Array,
			},
			testProcessing: {
				default: false,
				type: Boolean,
			},
			testResults: {
				default: () => {},
				type: Object,
			},
		},
		data() {
			return {
				activeView: 'current',
				showListResults: false,
			}
		},
		computed: {
			activeFiltersForDisplay() {
				const filters = isEmpty(this.activeFilters)
					? this.$t('questions.filters.allQuestions')
					: this.activeFilters.join(', ')

				return this.$t('questions.filters.activeFiltersReview', {filters})
			},
			count() {
				return this.questions.length
			},
			currentQuestionNumber() {
				return (this.currentQuestion.page - 1) * this.meta.perPage + this.currentQuestion.index + 1
			},
			hasCurrentQuestion() {
				return !isEmpty(this.currentQuestion) && !!this.currentQuestion.id
			},
			toggleAnswersMessage() {
				const msg = this.showListResults ? 'hide' : 'show'
				return this.$t(`questions.solving.${msg}Answers`)
			},
			views() {
				return views
			},
		},
		methods: {
			buildTest(payload) {
				this.$emit('buildTest', payload)
			},
			changeQuestion(direction) {
				this.$emit('changeQuestion', direction)
			},
			checkQuestions() {
				this.$emit('checkQuestions')
			},
			changePage(page) {
				this.$emit('changePage', page)
			},
			questionNumber(index) {
				return (this.meta.currentPage - 1) * limit + index + 1
			},
			selectAnswer(payload) {
				this.$emit('selectAnswer', payload)
			},
			setQuestion(index) {
				this.$emit('setQuestion', {page: this.meta.currentPage, index})
				this.activeView = 'current'
			},
			onVerify(payload) {
				this.$emit('verify', payload)
			},
		},
		watch: {
			activeFilters() {
				this.showListResults = false
			},
		}
	}
</script>
