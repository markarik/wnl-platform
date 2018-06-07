<template>
	<div class="wnl-app-layout">
		<wnl-questions-navigation/>
		<div class="wnl-middle wnl-app-layout-main">
			<div class="scrollable-main-container">
				<div class="questions-header">
					<div class="questions-breadcrumbs">
						<div class="breadcrumb">
							<span class="icon is-small"><i
									class="fa fa-check-square-o"></i></span>
						</div>
						<div class="breadcrumb">
							<span class="icon is-small"><i
									class="fa fa-angle-right"></i></span>
							<span>{{$t('questions.nav.solving')}}</span>
						</div>
					</div>
					<a v-if="isMobile" slot="heading"
					   class="mobile-show-active-filters" @click="toggleChat">
						<span>{{$t('questions.filters.show')}}</span>
						<span class="icon is-tiny">
							<i class="fa fa-sliders"></i>
						</span>
					</a>
				</div>
				<wnl-questions-solving
						v-if="computedQuestionsList.length > 0 || !fetchingQuestions"
						:activeFilters="activeFiltersNames"
						:currentQuestion="currentQuestion"
						:loading="fetchingQuestions || fetchingFilters"
						:getReaction="computedGetReaction"
						:isMobile="isMobile"
						:meta="meta"
						:questionsListCount="matchedQuestionsCount"
						:questionsCurrentPage="questionsCurrentPage"
						:presetOptions="presetOptionsToPass"
						:testMode="testMode"
						:testQuestions="testQuestions"
						:testProcessing="testProcessing"
						:testResults="testResults"
						@buildTest="buildTest"
						@changeQuestion="onChangeQuestion"
						@changePage="onChangePage"
						@checkQuiz="verifyCheckQuestions"
						@endQuiz="verifyEndQuiz"
						@selectAnswer="onSelectAnswer"
						@setQuestion="setQuestion"
						@verify="onVerify"
				/>
				<div v-else class="text-loader">
					<wnl-text-loader/>
				</div>
			</div>
		</div>
		<wnl-sidenav-slot
				:isDetached="!isChatMounted"
				:isVisible="isLargeDesktop || isChatVisible"
				:hasChat="true"
		>
			<wnl-questions-filters
					v-show="!testMode"
					:loading="fetchingQuestions || fetchingFilters"
					:activeFilters="activeFilters"
					:fetchingData="fetchingQuestions || fetchingFilters"
					:filters="filters"
					@activeFiltersChanged="onActiveFiltersChanged"
					@search="onSearch"
			/>
		</wnl-sidenav-slot>
		<div v-if="!testMode && !isLargeDesktop && isChatToggleVisible"
			 class="wnl-chat-toggle">
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
	import {isEmpty, get} from 'lodash'
	import {mapGetters, mapActions, mapMutations} from 'vuex'
	import {QUESTIONS_SET_TOKEN as setToken} from 'js/store/mutations-types'

	import ActiveFilters from 'js/components/questions/ActiveFilters'
	import QuizWidget from 'js/components/quiz/QuizWidget'
	import QuestionsFilters from 'js/components/questions/QuestionsFilters'
	import QuestionsNavigation from 'js/components/questions/QuestionsNavigation'
	import QuestionsSolving from 'js/components/questions/QuestionsSolving'
	import QuestionsTest from 'js/components/questions/QuestionsTest'
	import QuestionsSearch from 'js/components/questions/QuestionsSearch'
	import SidenavSlot from 'js/components/global/SidenavSlot'

	import {scrollToTop} from 'js/utils/animations'
	import {swalConfig} from 'js/utils/swal'

	export default {
		name: 'QuestionsList',
		props: {
			presetFilters: {
				default: () => [],
				type: Array,
			},
			presetOptions: {
				default: () => {},
				type: Object,
			}
		},
		components: {
			'wnl-active-filters': ActiveFilters,
			'wnl-questions-navigation': QuestionsNavigation,
			'wnl-quiz-widget': QuizWidget,
			'wnl-questions-filters': QuestionsFilters,
			'wnl-sidenav-slot': SidenavSlot,
			'wnl-questions-test': QuestionsTest,
			'wnl-questions-solving': QuestionsSolving,
			'wnl-questions-search': QuestionsSearch
		},
		data() {
			return {
				fetchingFilters: false,
				fetchingQuestions: false,
				orderedQuestionsList: [],
				showBuilder: false,
				testMode: false,
				testProcessing: false,
				testResults: {},
				reactionsFetched: false,
				presetOptionsToPass: isEmpty(this.presetOptions) ? {} : this.presetOptions,
				searchPhrase: ''
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
				'activeFiltersObjects',
				'currentQuestion',
				'filters',
				'getQuestion',
				'getPage',
				'getReaction',
				'matchedQuestionsCount',
				'meta',
				'questionsCurrentPage',
				'questionsList',
				'testQuestions',
				'testQuestionsUnanswered',
				'getSafePage',
			]),
			activeFiltersNames() {
				return this.activeFiltersObjects.map(filter => {
					if (!filter) return
					if (filter.type === 'search') {
						return filter.items[0] && filter.items[0].value && `Fraza: ${filter.items[0].value}`
					}
					return filter.hasOwnProperty('name')
						? filter.name
						: filter.hasOwnProperty('message')
							? this.$t(`questions.filters.items.${filter.message}`)
							: this.$t(`questions.filters.items.${filter.value}`)
				})
			},
			computedGetReaction() {
				return this.reactionsFetched ? this.getReaction : () => {
				}
			},
			computedQuestionsList() {
				return this.orderedQuestionsList.length ? this.orderedQuestionsList : this.questionsList
			},
			examMode() {
				return !!this.presetOptionsToPass.examMode && this.testMode
			},
			examTagId() {
				return this.presetOptions.examTagId || 0
			},
			initialFilters() {
				let filters = !isEmpty(this.presetFilters) ? this.presetFilters : this.activeFilters

				if (this.presetOptions.examMode && this.examTagId) {
					const filterName = 'by_taxonomy-exams';
					const filterIndex = this.filters[filterName].items.findIndex(item => {
						return item.value === this.examTagId
					})
					filters = filterIndex > -1 ? [`${filterName}.items[${filterIndex}]`] : filters
				}

				return filters
			}
		},
		methods: {
			...mapActions(['toggleChat', 'toggleOverlay']),
			...mapActions('questions', [
				'addFilter',
				'activeFiltersSet',
				'activeFiltersToggle',
				'changeCurrentQuestion',
				'checkQuestions',
				'getPosition',
				'fetchQuestionData',
				'fetchQuestions',
				'fetchPage',
				'fetchTestQuestions',
				'fetchDynamicFilters',
				'fetchQuestionsReactions',
				'resetCurrentQuestion',
				'resetPages',
				'resetTest',
				'resolveQuestion',
				'saveQuestionsResults',
				'savePosition',
				'selectAnswer',
				'setPage',
				'fetchActiveFilters'
			]),
			...mapMutations('questions', {setToken}),
			buildTest({count}) {
				const text = this.presetOptionsToPass.hasOwnProperty('loadingText')
					? this.presetOptionsToPass.loadingText
					: 'testBuilding'

				this.switchOverlay(true, 'testBuilding', text)
				this.resetTest()
				this.testMode = true
				this.fetchTestQuestions({
					activeFilters: this.activeFilters,
					count: count,
					randomize: !this.examMode,
				}).then(() => this.switchOverlay(false, 'testBuilding'))
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
			confirmQuizEnd(text = '') {
				const config = swalConfig({
					confirmButtonText: this.$t('questions.solving.confirm.yes'),
					cancelButtonText: this.$t('questions.solving.confirm.no'),
					reverseButtons: true,
					showCancelButton: true,
					showConfirmButton: true,
					title: this.$t('questions.solving.confirm.title'),
					type: 'question',
				})

				if (!isEmpty(text)) {
					config.text = text
				}

				return new Promise((resolve, reject) => {
					this.$swal(config)
						.then(() => resolve(), (dismiss) => {
							if (dismiss === 'cancel') {
								return reject()
							}
						})
						.catch(e => reject())
				})
			},
			endQuiz() {
				this.testMode = this.testProcessing = false
				this.testResults = {}
				this.resetTest()
			},
			fetchMatchingQuestions() {
				return this.fetchQuestions({filters: this.activeFilters})
					.catch(error => $wnl.logger.error(error))
			},
			onActiveFiltersChanged(payload) {
				this.fetchingFilters = true
				this.activeFiltersToggle(payload)
					.then(() => {
						if (!payload.refresh) return false

						this.resetCurrentQuestion()
						this.setToken()
						this.resetPages()
						this.fetchDynamicFilters()
						return this.fetchMatchingQuestions()
					})
					.then(() => {
						this.fetchingFilters = false

						if (!payload.refresh) return false
						this.fetchQuestionsReactions(this.getPage(1))
					})
			},
			onChangeQuestion(step) {
				const currentIndex = this.currentQuestion.index
				const currentPage  = this.currentQuestion.page
				const perPage      = this.meta.perPage
				const pageStep     = Math.sign(step) * Math.ceil(Math.abs(step / perPage))

				let newIndex, newPage

				if (step > 0 && currentIndex + step >= this.questionsCurrentPage.length) {
					newIndex = 0
					newPage  = currentPage === this.meta.lastPage ? 1 : currentPage + pageStep
				}
				else if (step < 0 && currentIndex === 0) {
					newIndex = currentPage === 1 ? -1 : perPage - 1
					newPage  = currentPage === 1 ? this.meta.lastPage : currentPage + pageStep
				} else {
					newPage  = currentPage
					newIndex = currentIndex + step
				}

				this.setQuestion({page: newPage, index: newIndex})
			},
			onChangePage(page) {
				scrollToTop()
				this.changePage(page)
			},
			onSelectAnswer(payload) {
				payload.answer === this.getQuestion(payload.id).selectedAnswer
				&& !this.testMode
					? this.onVerify(payload.id) || (payload.position && this.savePosition({position: payload.position}))
					: this.selectAnswer(payload)
			},
			onVerify(questionId) {
				this.resolveQuestion(questionId)
				this.saveQuestionsResults({questions: [questionId]})
			},
			performCheckQuestions() {
				scrollToTop()
				this.testProcessing = true
				this.checkQuestions({examMode: this.examMode, examTagId: this.examTagId}).then(results => {
					this.testResults         = results
					this.testProcessing      = false
					this.testMode            = false
					this.presetOptionsToPass = {}
				})
			},
			setQuestion({page, index}) {
				this.switchOverlay(true, 'currentQuestion')
				this.changePage(page)
				// last page may change after fetching the page
				// when "nierozwiązane pytania" filter is active
					.then(() => this.changeCurrentQuestion({
						page: this.getSafePage(page),
						index
					}))
					.then(question => {
						this.switchOverlay(false, 'currentQuestion')
						this.fetchQuestionData(question.id)
						this.savePosition({
							position: {
								page: this.getSafePage(page),
								index
							}
						})
					})
			},
			setupFilters(activeFilters = []) {
				if (!isEmpty(this.filters)) return Promise.resolve(this.filters)

				return this.fetchDynamicFilters()
			},
			switchOverlay(display, source = 'filters', message = 'questions') {
				this.fetchingQuestions = display
				this.toggleOverlay({
					source,
					display,
					text: this.$t(`ui.loading.${message}`)
				})
			},
			toggleBuilder() {
				this.showBuilder = !this.showBuilder
			},
			verifyCheckQuestions({unansweredCount}) {
				if (unansweredCount) {
					this.confirmQuizEnd(this.$t('questions.solving.confirm.unanswered', {
						count: unansweredCount
					})).then(() => false)
						.catch(() => this.performCheckQuestions())
				} else {
					this.performCheckQuestions()
				}
			},
			verifyEndQuiz() {
				if (this.testMode) {
					this.confirmQuizEnd()
						.then(() => false)
						.catch(() => this.endQuiz())
				} else {
					this.endQuiz()
				}
			},
			onSearch(phrase) {
				if (phrase.trim() !== '') {
					this.addFilter(phrase)
					.then(() => {
						return this.activeFiltersToggle({
							filter: `search.${phrase}`,
							active: true,
						})
					})
					.then(() => {
						this.fetchingFilters = true

						this.resetCurrentQuestion()
						this.setToken()
						this.resetPages()
						return Promise.all([
							this.fetchDynamicFilters(),
							this.fetchMatchingQuestions()
						])
					}).then(() => Promise.all([
						this.fetchQuestionsReactions(this.getPage(this.meta.currentPage)),
						this.fetchQuestionData(this.currentQuestion.id)
					])).then(() => {
						this.fetchingFilters = false
					})
				}
			}
		},
		mounted() {
			const hasPresetFilters = !isEmpty(this.presetFilters)

			this.switchOverlay(true)
			this.fetchingFilters = true
			this.setupFilters().then(() => {
				return new Promise(resolve => {
					if (hasPresetFilters) {
						this.activeFiltersSet(this.presetFilters)
						resolve()
					} else {
						this.fetchActiveFilters().then(resolve)
					}
				})
				.then(() => {
					this.fetchingFilters = false
					this.resetCurrentQuestion()
					this.setToken()
				})
				.then(this.fetchDynamicFilters)
				.then(this.getPosition)
				.then(({data = {}}) => {
					return new Promise((resolve, reject) => {
						this.fetchQuestions({
							saveFilters: false,
							useSavedFilters: false,
							page: (data.position && data.position.page) || 1,
							filters: this.initialFilters,
						}).then(() => resolve(data))
					})
				})
				.then(({position}) => {
					position && this.changeCurrentQuestion(position)
					this.switchOverlay(false)
				})
				.then(() => this.fetchQuestionsReactions(this.getPage(1)))
				.then(() => this.reactionsFetched = true)
				.then(() => this.fetchQuestionData(this.currentQuestion.id))
				.catch(e => {
					$wnl.logger.error(e)
					this.fetchingFilters = false
					this.switchOverlay(false)
				})
			})
		},
		beforeRouteLeave(to, from, next) {
			if (this.testMode) {
				this.confirmQuizEnd()
					.then(() => next(false))
					.catch(() => next())
			} else {
				next()
			}
		},
		watch: {
			testQuestionsCount() {
				this.estimatedTime = timeBaseOnQuestions(this.testQuestionsCount)
			},
			'$route.query.chatChannel'(newVal) {
				newVal && !this.isChatVisible && this.toggleChat();
			}
		}
	}
</script>
