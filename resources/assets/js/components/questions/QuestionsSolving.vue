<template>
	<div class="questions-solving-container" :class="{'is-mobile': isMobile}">
		<div class="questions-solving-view" v-if="!testMode">
			<div class="tabs" v-if="!isMobile">
				<ul>
					<li
						v-for="(view, index) in views"
						:key="index"
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
			<div v-else class="questions-solving-select control">
				<span class="select">
					<select @input="changeViewWithSelect">
						<option v-for="(view, index) in views"
							:key="index"
							:value="view.name"
							:selected="view.name === activeView"
						>
							<span class="icon is-small"><i class="fa" :class="view.icon"></i></span> {{$t(`questions.solving.tabs.${view.name}`, {
								count: questionsListCount,
								current: questionNumber(currentQuestion.index)
							})}}
						</option>
					</select>
				</span>
			</div>
		</div>
		<div class="questions-list-info" v-if="!testMode">
			<div class="active-filters">
				{{activeFiltersForDisplay}}
			</div>
			<a v-if="activeView === VIEWS.LIST"
				class="button is-small is-outlined is-primary"
				@click="showListResults = !showListResults"
			>
				{{toggleAnswersMessage}}
			</a>
		</div>

		<div v-if="hasCurrentQuestion" ref="view">
			<!-- Current Question -->
			<div v-if="activeView === VIEWS.CURRENT_QUESTION">
				<wnl-active-question
					:module="module"
					:getReaction="getReaction"
					:question="currentQuestion"
					:questionNumber="currentQuestionNumber"
					:allQuestionsCount="questionsListCount"
					@changeQuestion="changeQuestion"
					@selectAnswer="selectAnswer"
					@verify="onVerify"
					@userEvent="proxyUserEvent"
				/>
			</div>

			<!-- List -->
			<div v-if="activeView === VIEWS.LIST" class="questions-list">
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
							<a @click="setQuestion(index)">
								{{ $t('questions.solving.setAsCurrent') }}
							</a>
						</span>
						<span class="question-id">#{{question.id}}</span>
					</div>
					<wnl-quiz-question
						:class="`quiz-question-${question.id}`"
						:getReaction="getReaction"
						:id="question.id"
						:module="module"
						:question="question"
						:readOnly="showListResults"
						:hideComments="true"
						@selectAnswer="selectAnswer(...arguments, {position: {index, page: meta.currentPage}})"
						@userEvent="proxyUserEvent"
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
			<div v-if="activeView === VIEWS.TEST_YOURSELF">
				<wnl-questions-test-builder
					:getReaction="getReaction"
					:questions="testQuestions"
					:questionsPoolSize="questionsListCount"
					:presetOptions="presetOptions"
					:testMode="testMode"
					:testProcessing="testProcessing"
					:testResults="testResults"
					@buildTest="buildTest"
					@selectAnswer="selectAnswer"
					@checkQuiz="(payload) => $emit('checkQuiz', payload)"
					@endQuiz="$emit('endQuiz')"
					@userEvent="proxyUserEvent"
					@updateTime="(payload) => $emit('updateTime', payload)"
					@taxonomyTermAttached="(payload) => $emit('taxonomyTermAttached', payload)"
					@taxonomyTermDetached="(payload) => $emit('taxonomyTermDetached', payload)"
				/>
			</div>
		</div>

		<div v-else-if="!loading" class="has-text-centered margin vertical metadata">
			{{$t('questions.zeroState')}}
		</div>
		<div v-else>
			<wnl-text-loader>{{$t('ui.loading.questions')}}</wnl-text-loader>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

	.questions-solving-container
		&.is-mobile
			.questions-solving-view
				+flex-center()

			.questions-list-info
				align-items: center
				flex-direction: column
				text-align: center
				margin: -$margin-medium 0 0

				.active-filters
					line-height: $line-height-minus
					margin-bottom: $margin-small

	.tabs
		font-size: $font-size-minus-1

		.is-active
			font-weight: $font-weight-regular

	.questions-solving-select
		height: 4em

	.questions-list-info
		align-items: flex-start
		display: flex
		justify-content: space-between
		margin: $margin-medium 0 0

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
			width: 100%

			.wnl-quiz-question
				margin: 0
</style>

<script>
import {isEmpty, isNumber} from 'lodash';

import ActiveQuestion from 'js/components/questions/ActiveQuestion';
import QuestionsTestBuilder from 'js/components/questions/QuestionsTestBuilder';
import QuizQuestion from 'js/components/quiz/QuizQuestion';
import Pagination from 'js/components/global/Pagination';
import { scrollToElement } from 'js/utils/animations';
import emits_events from 'js/mixins/emits-events';
import {VIEWS} from 'js/consts/questionsSolving';

const views = [
	{
		name: VIEWS.CURRENT_QUESTION,
		icon: 'fa-check'
	},
	{
		name: VIEWS.LIST,
		icon: 'fa-list'
	},
	{
		name: VIEWS.TEST_YOURSELF,
		icon: 'fa-clock-o'
	},
];

const limit = 30;

export default {
	name: 'QuestionsSolving',
	components: {
		'wnl-active-question': ActiveQuestion,
		'wnl-questions-test-builder': QuestionsTestBuilder,
		'wnl-quiz-question': QuizQuestion,
		'wnl-pagination': Pagination,
	},
	mixins: [emits_events],
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
		isMobile: {
			default: false,
			type: Boolean,
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
		presetOptions: {
			default: () => {},
			type: Object,
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
			activeView: VIEWS.CURRENT_QUESTION,
			showListResults: false,
			VIEWS,
		};
	},
	computed: {
		activeFiltersForDisplay() {
			const filters = isEmpty(this.activeFilters)
				? this.$t('questions.filters.allQuestions')
				: this.activeFilters.join(', ');
			return this.$t('questions.filters.activeFiltersReview', {filters});
		},
		count() {
			return this.questions.length;
		},
		currentQuestionNumber() {
			return (this.currentQuestion.page - 1) * this.meta.perPage + this.currentQuestion.index + 1;
		},
		hasCurrentQuestion() {
			return !isEmpty(this.currentQuestion) && !!this.currentQuestion.id;
		},
		toggleAnswersMessage() {
			const msg = this.showListResults ? 'hide' : 'show';
			return this.$t(`questions.solving.${msg}Answers`);
		},
		views() {
			return views;
		},
	},
	methods: {
		buildTest(payload) {
			this.$emit('buildTest', payload);
		},
		changeQuestion(direction) {
			this.$emit('changeQuestion', direction);
		},
		checkQuestions() {
			this.$emit('checkQuestions');
		},
		changePage(page) {
			this.$emit('changePage', page);
		},
		changeViewWithSelect(event) {
			this.activeView = event.target.value;
		},
		questionNumber(index) {
			return isNumber(index)
				? (this.meta.currentPage - 1) * limit + index + 1
				: '';
		},
		selectAnswer(payload, position) {
			this.$emit('selectAnswer', {...payload, ...position});
		},
		setQuestion(index) {
			this.$emit('setQuestion', {page: this.meta.currentPage, index});
			this.activeView = VIEWS.CURRENT_QUESTION;
		},
		onVerify(payload) {
			this.$emit('verify', payload);
		},
	},
	mounted() {
		if (this.presetOptions.hasOwnProperty('activeView')) {
			this.activeView = this.presetOptions.activeView;
		}
		this.$emit('activeViewChange', this.activeView);
	},
	watch: {
		activeFilters() {
			this.showListResults = false;
		},
		activeView() {
			if (!this.$refs.view) return false;

			scrollToElement(this.$refs.view, 200);

			this.$emit('activeViewChange', this.activeView);
		},
		presetOptions() {
			if (this.presetOptions.hasOwnProperty('activeView')) {
				this.activeView = this.presetOptions.activeView;
			}
			this.$emit('activeViewChange', this.activeView);
		}
	}
};
</script>
