<template>
	<div class="wnl-app-layout">
		<wnl-questions-navigation/>
		<div class="wnl-middle wnl-app-layout-main">
			<div class="scrollable-main-container">
				<wnl-active-filters
					:activeFilters="activeFilters"
					:loading="fetchingQuestions"
					:filters="filters"
					:matchedCount="matchedQuestionsCount"
					:totalCount="allQuestionsCount"
					@activeFiltersChanged="onActiveFiltersChanged"
					@fetchMatchingQuestions="onFetchMatchingQuestions"
				>
					<a v-if="isMobile" slot="heading" class="mobile-show-active-filters" @click="toggleChat">
						<span>{{$t('questions.filters.show')}}</span>
						<span class="icon is-tiny">
							<i class="fa fa-sliders"></i>
						</span>
					</a>
				</wnl-active-filters>
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
						<input type="text" name="time" :value="estimatedTime"/>
						<span>minut</span>
					</section>
					<button @click="buildTest">No to GO!</button>
				</div>
				<!-- TODO Implement zero state -->
				<wnl-quiz-widget
					v-if="questionsList.length > 0"
					module="questions"
					:questions="questionsList"
					:getReaction="getReaction"
					@changeQuestion="performChangeQuestion"
					@verify="onVerify"
					@selectAnswer="selectAnswer"
				></wnl-quiz-widget>
			</div>
		</div>
		<wnl-sidenav-slot
			:isDetached="!isChatMounted"
			:isVisible="isLargeDesktop || isChatVisible"
			:hasChat="true"
		>
			<wnl-questions-filters
				:activeFilters="activeFilters"
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

	.active-filters
		margin-bottom: $margin-base

	.mobile-show-active-filters
		align-items: center
		display: flex
		font-size: $font-size-minus-2
		text-transform: uppercase
</style>

<script>
	import {mapGetters, mapActions} from 'vuex'

	import ActiveFilters from 'js/components/questions/ActiveFilters'
	import QuizWidget from 'js/components/quiz/QuizWidget'
	import QuestionsFilters from 'js/components/questions/QuestionsFilters'
	import QuestionsNavigation from 'js/components/questions/QuestionsNavigation'
	import SidenavSlot from 'js/components/global/SidenavSlot'

	import {timeBaseOnQuestions} from 'js/services/testBuilder'

	export default {
		components: {
			'wnl-active-filters': ActiveFilters,
			'wnl-questions-navigation': QuestionsNavigation,
			'wnl-quiz-widget': QuizWidget,
			'wnl-questions-filters': QuestionsFilters,
			'wnl-sidenav-slot': SidenavSlot,
		},
		data() {
			return {
				estimatedTime: timeBaseOnQuestions(30),
				fetchingQuestions: false,
				orderedQuestionsList: [],
				showBuilder: false,
				testQuestionsCount: 30,
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
				'allQuestionsCount',
				'filters',
				'getReaction',
				'questions',
				'matchedQuestionsCount',
			]),
			highlightedQuestion() {
				return this.questionsList[0]
			},
			questionsList() {
				return this.orderedQuestionsList.length ? this.orderedQuestionsList : Object.values(this.questions)
			}
		},
		methods: {
			...mapActions(['toggleChat']),
			...mapActions('questions', [
				'activeFiltersReset',
				'activeFiltersToggle',
				'fetchQuestionData',
				'fetchQuestions',
				'fetchQuestionsCount',
				'fetchTestQuestions',
				'fetchDynamicFilters',
				'fetchMatchingQuestions',
				'selectAnswer',
				'resolveQuestion',
			]),
			debouncedFetchMatchingQuestions: _.debounce(function() {
				this.fetchMatchingQuestions(this.activeFilters)
			}, 500),
			onActiveFiltersChanged(payload) {
				this.activeFiltersToggle(payload)
					// .then(this.debouncedFetchMatchingQuestions)
			},
			onFetchMatchingQuestions() {
				this.fetchingQuestions = true
				this.fetchMatchingQuestions(this.activeFilters)
					.then(() => this.fetchingQuestions = false)
			},
			onVerify(questionId) {
				this.resolveQuestion(questionId)
				// TODO record answer in DB
			},
			performChangeQuestion(index) {
				const beforeIndex = this.questionsList.slice(0, index);
				const afterIndex = this.questionsList.slice(index)

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
				}).then(() => {
					this.$router.push({
						name: 'questionsTest',
						params: {
							questions: Object.values(this.questions),
							time: this.estimatedTime,
							onSelectAnswer: this.selectAnswer
						}

					})
				})
			}
		},
		mounted() {
			Promise.all([this.fetchQuestions(), this.fetchDynamicFilters(), this.fetchQuestionsCount()])
		},
		watch: {
			highlightedQuestion() {
				if (!this.highlightedQuestion.answers) {
					// TODO loading state
					this.fetchQuestionData(this.highlightedQuestion.id)
				}
			},
			testQuestionsCount() {
				this.estimatedTime = timeBaseOnQuestions(this.testQuestionsCount)
			}
		}
	}
</script>
