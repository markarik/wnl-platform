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
					:questionsListCount="matchedQuestionsCount"
					:questionsCurrentPage="questionsCurrentPage"
					:getReaction="computedGetReaction"
					:meta="meta"
					@changeQuestion="onChangeQuestion"
					@changePage="onChangePage"
					@selectAnswer="onSelectAnswer"
					@setQuestion="setQuestion"
					@verify="onVerify"
				/>
				<button @click="toggleBuilder">Zbuduj zestaw</button>
				<div v-show="showBuilder">
					<section>
						<p>Na ile pytań chcesz odpowiedzieć?</p>
						<input type="radio" name="count" value="30" id="countThirty" v-model="testQuestionsCount"/>
						<label for="countThirty">30 pytań</label>
						<input type="radio" name="count" value="50" id="countFifty" v-model="testQuestionsCount"/>
						<label for="countFifty">50 pytań</label>
						<input type="radio" name="count" value="100" id="countHundred" v-model="testQuestionsCount"/>
						<label for="countHundred">100 pytań</label>
						<input type="radio" name="count" value="150" id="countOneFifty" v-model="testQuestionsCount"/>
						<label for="countNinty">150 pytań</label>
						<input type="radio" name="count" value="120" id="countTwoHundred" v-model="testQuestionsCount"/>
						<label for="countTwoHundred">200 pytań</label>
					</section>
					<section>
						<label for="time">Ile czasu chcesz poświęcić?</label>
						<input type="text" name="time" v-model="estimatedTime"/>
						<span>minut</span>
					</section>
					<button @click="buildTest">No to GO!</button>
				</div>

				<!-- <div class="has-text-centered margin vertical metadata" v-else>
					{{$t('questions.zeroState')}}
				</div> -->
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
		display: flex
		margin-right: $margin-base

		.breadcrumb
			max-width: 200px
			overflow-x: hidden
			text-overflow: ellipsis
			white-space: nowrap
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
				'questionsCurrentPage',
				'filters',
				'getReaction',
				'matchedQuestionsCount',
				'meta',
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
			overlay() {
				this.toggleOverlay({source: 'filters', display: this.fetchingQuestions})
			},
		},
		methods: {
			...mapActions(['toggleChat', 'toggleOverlay']),
			...mapActions('questions', [
				'activeFiltersSet',
				'activeFiltersToggle',
				'changePage',
				'changeCurrentQuestion',
				'fetchQuestionData',
				'fetchQuestions',
				'fetchQuestionsCount',
				'fetchTestQuestions',
				'fetchDynamicFilters',
				'fetchQuestionsReactions',
				'selectAnswer',
				'refreshCurrentQuestion',
				'resetCurrentQuestion',
				'resetPages',
				'resolveQuestion',
				'checkQuestions',
				'saveQuestionsResults'
			]),
			debouncedFetchMatchingQuestions: _.debounce(function() {
				this.switchOverlay(true)
				return this.fetchQuestions({filters: this.activeFilters})
					.then(() => this.switchOverlay(false))
			}, 500),
			onActiveFiltersChanged(payload) {
				this.activeFiltersToggle(payload)
					.then(() => {
						this.resetPages()
						return this.debouncedFetchMatchingQuestions()
					})
					.then(() => {
						this.resetCurrentQuestion()
					})
			},
			onChangeQuestion(direction) {
				const currentIndex = this.currentQuestion.index
				const currentPage = this.currentQuestion.page

				if (!!direction) {
					// Next
					if (currentIndex + 1 >= this.meta.perPage) {
						if (currentPage === this.meta.lastPage) return false
						const newPage = currentPage + 1

						this.changePage(newPage).then(() => {
							this.changeCurrentQuestion({page: newPage, index: 0})
						})
					} else {
						this.changeCurrentQuestion({page: currentPage, index: currentIndex + 1})
					}
				} else {
					// Previous
					if (currentIndex === 0) {
						if (currentPage === 1) return false
						const newPage = currentPage - 1

						this.changePage(newPage).then(() => {
							this.changeCurrentQuestion({page: newPage, index: this.meta.perPage - 1})
						})
					} else {
						this.changeCurrentQuestion({page: currentPage, index: currentIndex - 1})
					}
				}
			},
			onChangePage(page) {
				this.changePage(page)
			},
			onFetchMatchingQuestions() {
				this.fetchQuestions({filters: this.activeFilters})
					.then(() => {
						this.resetCurrentQuestion()
						this.fetchingQuestions = false
					})
			},
			onSelectAnswer(payload) {
				this.selectAnswer(payload)
				this.refreshCurrentQuestion()
			},
			onVerify(questionId) {
				this.resolveQuestion(questionId)
				this.refreshCurrentQuestion()
				this.saveQuestionsResults([questionId])
			},
			setQuestion({page, index}) {
				this.changeCurrentQuestion({page, index})
			},
			switchOverlay(display) {
				this.toggleOverlay({source: 'filters', display, text: this.$t('ui.loading.questions')})
			},
			performChangeQuestion(index) {
				const beforeIndex = this.computedQuestionsList.slice(0, index);
				const afterIndex = this.computedQuestionsList.slice(index)

				this.orderedQuestionsList = [...afterIndex, ...beforeIndex]
				// TODO if we decide on pagination we can fetch new question here
			},
			toggleBuilder() {
				this.showBuilder = !this.showBuilder
			},
			buildTest() {
				this.fetchTestQuestions({
					activeFilters: this.activeFilters,
					count: this.testQuestionsCount
				}).then(() => this.testMode = true)
			}
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
					this.fetchQuestionsReactions(this.questionsList.map(question => question.id))
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
