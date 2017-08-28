<template>
	<wnl-questions-test v-if="testMode"
		:questions="questionsList"
		:time="estimatedTime * 60"
		:onSelectAnswer="selectAnswer"
		:onCheckQuiz="checkQuestions"
		:getReaction="getReaction"
		@endTest="testMode = false"
	/>
	<div class="wnl-app-layout" v-else>
		<wnl-questions-navigation/>
		<div class="wnl-middle wnl-app-layout-main">
			<div class="scrollable-main-container">
				<div class="questions-header">
					<div class="questions-breadcrumbs">
						<div class="breadcrumb">
							<span class="icon is-small"><i class="fa fa-check-square-o"></i></span>
						</div>
						<div class="breadcrumb">
							<span class="icon is-small"><i class="fa fa-angle-right"></i></span>
							<span>{{$t('questions.nav.solving')}}</span>
						</div>
					</div>
					<a v-if="isMobile" slot="heading" class="mobile-show-active-filters" @click="toggleChat">
						<span>{{$t('questions.filters.show')}}</span>
						<span class="icon is-tiny">
							<i class="fa fa-sliders"></i>
						</span>
					</a>
				</div>
				<wnl-questions-solving
					v-if="computedQuestionsList.length > 0"
					:activeFilters="activeFiltersNames"
					:currentQuestion="currentQuestion"
					:loading="fetchingQuestions"
					:getReaction="computedGetReaction"
					:meta="meta"
					:questionsListCount="matchedQuestionsCount"
					:questionsCurrentPage="questionsCurrentPage"
					@buildTest="buildTest"
					@changeQuestion="onChangeQuestion"
					@changePage="changePage"
					@selectAnswer="onSelectAnswer"
					@setQuestion="setQuestion"
					@verify="onVerify"
				/>
				<div v-else class="text-loader"><wnl-text-loader/></div>
			</div>
		</div>
		<wnl-sidenav-slot
			:isDetached="!isChatMounted"
			:isVisible="isLargeDesktop || isChatVisible"
			:hasChat="true"
		>
			<wnl-questions-filters
				:activeFilters="activeFilters"
				:fetchingQuestions="fetchingQuestions"
				:filters="filters"
				@activeFiltersChanged="onActiveFiltersChanged"
			/>
		</wnl-sidenav-slot>
		<div v-if="!isLargeDesktop && isChatToggleVisible" class="wnl-chat-toggle">
			<span class="icon is-big" @click="toggleChat">
				<i class="fa fa-sliders"></i>
				<span>{{$t('questions.filters.show')}}</span>
			</span>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-app-layout-main
		width: 100%
		max-width: initial

	.mobile-show-active-filters
		font-size: $font-size-minus-2
		text-transform: uppercase

	.questions-header
		align-items: center
		display: flex
		justify-content: space-between
		margin-bottom: $margin-small

	.questions-breadcrumbs
		align-items: center
		color: $color-gray-dimmed
		font-size: $font-size-minus-1
		display: flex
		margin-right: $margin-base

		.breadcrumb
			max-width: 200px
			overflow-x: hidden
			text-overflow: ellipsis
			white-space: nowrap

	.text-loader
		display: flex
		justify-content: center
</style>

<script>
	import {isEmpty} from 'lodash'
	import {mapGetters, mapActions} from 'vuex'

	import ActiveFilters from 'js/components/questions/ActiveFilters'
	import QuizWidget from 'js/components/quiz/QuizWidget'
	import QuestionsFilters from 'js/components/questions/QuestionsFilters'
	import QuestionsNavigation from 'js/components/questions/QuestionsNavigation'
	import QuestionsSolving from 'js/components/questions/QuestionsSolving'
	import QuestionsTest from 'js/components/questions/QuestionsTest'
	import SidenavSlot from 'js/components/global/SidenavSlot'

	import {timeBaseOnQuestions} from 'js/services/testBuilder'

	export default {
		name: 'QuestionsList',
		props: {
			presetFilters: {
				default: () => [],
				type: Array,
			},
		},
		components: {
			'wnl-active-filters': ActiveFilters,
			'wnl-questions-navigation': QuestionsNavigation,
			'wnl-quiz-widget': QuizWidget,
			'wnl-questions-filters': QuestionsFilters,
			'wnl-sidenav-slot': SidenavSlot,
			'wnl-questions-test': QuestionsTest,
			'wnl-questions-solving': QuestionsSolving,
		},
		data() {
			return {
				estimatedTime: timeBaseOnQuestions(30),
				fetchingQuestions: false,
				orderedQuestionsList: [],
				showBuilder: false,
				testQuestionsCount: 30,
				testMode: false,
				reactionsFetched: false
			}
		},
		computed: {
			...mapGetters([
				'isChatMounted',
				'isChatToggleVisible',
				'isChatVisible',
				'isMobile',
				'isLargeDesktop',
				'isSidenavMounted',
				'isSidenavVisible',
			]),
			...mapGetters('questions', [
				'activeFilters',
				'activeFiltersNames',
				'currentQuestion',
				'filters',
				'getPage',
				'getReaction',
				'matchedQuestionsCount',
				'meta',
				'questionsCurrentPage',
				'questionsList',
			]),
			computedGetReaction() {
				return this.reactionsFetched ? this.getReaction : () => {}
			},
			computedQuestionsList() {
				return this.orderedQuestionsList.length ? this.orderedQuestionsList : this.questionsList
			},
			highlightedQuestion() {
				return this.questionsList[0]
			},
		},
		methods: {
			...mapActions(['toggleChat', 'toggleOverlay']),
			...mapActions('questions', [
				'activeFiltersSet',
				'activeFiltersToggle',
				'changeCurrentQuestion',
				'checkQuestions',
				'fetchQuestionData',
				'fetchQuestions',
				'fetchQuestionsCount',
				'fetchPage',
				'fetchTestQuestions',
				'fetchDynamicFilters',
				'fetchQuestionsReactions',
				'resetCurrentQuestion',
				'resetPages',
				'resolveQuestion',
				'saveQuestionsResults',
				'selectAnswer',
				'setPage',
			]),
			buildTest({count}) {
				this.fetchTestQuestions({
					activeFilters: this.activeFilters,
					count: count
				}).then(() => this.testMode = true)
			},
			changePage(page) {
				return new Promise((resolve, reject) => {
					if (this.getPage(page)) {
						this.setPage(page)
						resolve()
						return
					}

					this.switchOverlay(true)
					return this.fetchPage(page)
						.then(() => this.switchOverlay(false))
						.then(() => this.fetchQuestionsReactions(this.getPage(page)))
						.then(() => resolve())
				})
			},
			fetchMatchingQuestions() {
				this.switchOverlay(true)
				return this.fetchQuestions({filters: this.activeFilters})
					.catch(error => $wnl.logger.error(error))
					.then(() => this.switchOverlay(false))
			},
			onActiveFiltersChanged(payload) {
				this.activeFiltersToggle(payload)
					.then(() => {
						this.resetCurrentQuestion()
						this.resetPages()
						return this.fetchMatchingQuestions()
					})
					.then(() => {
						this.fetchQuestionsReactions(this.getPage(1))
					})
			},
			onChangeQuestion(step) {
				const currentIndex = this.currentQuestion.index
				const currentPage = this.currentQuestion.page
				const perPage = this.meta.perPage
				const pageStep = Math.sign(step) * Math.ceil(Math.abs(step/perPage))

				let newIndex, newPage

				if (step > 0 && currentIndex + step >= this.questionsCurrentPage.length) {
					newIndex = 0
					newPage = currentPage === this.meta.lastPage ? 1 : currentPage + pageStep
				}
				else if (step < 0 && currentIndex === 0) {
					newIndex = currentPage === 1 ? this.matchedQuestionsCount % this.meta.perPage - 1 : perPage - 1
					newPage = currentPage === 1 ? this.meta.lastPage : currentPage + pageStep
				} else {
					newPage = currentPage
					newIndex = currentIndex + step
				}

				this.setQuestion({page: newPage, index: newIndex})
			},
			onFetchMatchingQuestions() {
				this.resetCurrentQuestion()
				this.fetchQuestions({filters: this.activeFilters})
			},
			onSelectAnswer(payload) {
				this.selectAnswer(payload)
			},
			onVerify(questionId) {
				this.resolveQuestion(questionId)
				this.saveQuestionsResults([questionId])
			},
			setQuestion({page, index}) {
				this.switchOverlay(true, 'currentQuestion')
				this.changePage(page)
					.then(() => this.changeCurrentQuestion({page, index}))
					.then(question => {
						this.switchOverlay(false, 'currentQuestion')
						this.fetchQuestionData(question.id)
					})
			},
			switchOverlay(display, source = 'filters') {
				this.fetchingQuestions = display
				this.toggleOverlay({source, display, text: this.$t('ui.loading.questions')})
			},
			toggleBuilder() {
				this.showBuilder = !this.showBuilder
			},
		},
		mounted() {
			this.activeFiltersSet(this.presetFilters)
			Promise.all([
				this.fetchQuestions({filters: this.activeFilters}),
				this.fetchDynamicFilters(),
				this.fetchQuestionsCount(),
			])
				.then(() => {
					this.resetCurrentQuestion()
					return this.fetchQuestionsReactions(this.getPage(1))
				})
				.then(() => this.reactionsFetched = true)
		},
		watch: {
			highlightedQuestion(currentQuestion, previousQuestion = {}) {
				if (currentQuestion.id !== previousQuestion.id) {
					// TODO loading state
					this.fetchQuestionData(currentQuestion.id)
				}
			},
			testQuestionsCount() {
				this.estimatedTime = timeBaseOnQuestions(this.testQuestionsCount)
			}
		}
	}
</script>
