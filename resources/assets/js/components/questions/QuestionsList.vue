<template>
	<div class="wnl-app-layout">
		<wnl-sidenav-slot
			:isVisible="isSidenavVisible"
			:isDetached="!isSidenavMounted"
		>
			<wnl-main-nav :isHorizontal="!isSidenavMounted"></wnl-main-nav>
			<aside class="sidenav-aside">
				ELOSZKA
				<!-- <wnl-sidenav :items="getNavigation()" :options="navigationOptions"></wnl-sidenav> -->
			</aside>
		</wnl-sidenav-slot>
		<div class="wnl-middle wnl-app-layout-main">
			<div class="scrollable-main-container">
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
			:isVisible="isLargeDesktop"
			:hasChat="true"
		>
			<wnl-questions-filters/>
		</wnl-sidenav-slot>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-app-layout-main
		width: 100%
		max-width: initial

</style>

<script>
	import {mapGetters, mapActions} from 'vuex'

	import withChat from 'js/mixins/with-chat'

	import Sidenav from 'js/components/global/Sidenav'
	import SidenavSlot from 'js/components/global/SidenavSlot'
	import MainNav from 'js/components/MainNav'
	import QuizWidget from 'js/components/quiz/QuizWidget'
	import QuestionsFilters from 'js/components/questions/QuestionsFilters'

	export default {
		components: {
			'wnl-sidenav': Sidenav,
			'wnl-sidenav-slot': SidenavSlot,
			'wnl-main-nav': MainNav,
			'wnl-quiz-widget': QuizWidget,
			'wnl-questions-filters': QuestionsFilters
		},
		mixins: [withChat],
		data() {
			return {
				orderedQuestionsList: []
			}
		},
		computed: {
			...mapGetters(['isSidenavMounted', 'isSidenavVisible', 'isLargeDesktop', 'isChatMounted']),
			...mapGetters('questions', ['questions', 'getReaction']),
			highlightedQuestion() {
				return this.questionsList[0]
			},
			questionsList() {
				return this.orderedQuestionsList.length ? this.orderedQuestionsList : Object.values(this.questions)
			}
		},
		methods: {
			...mapActions('questions', [
				'fetchQuestions',
				'fetchQuestionData',
				'fetchDynamicFilters',
				'fetchMatchingQuestions',
				'selectAnswer',
				'resolveQuestion',
			]),
			performChangeQuestion(index) {
				const beforeIndex = this.questionsList.slice(0, index);
				const afterIndex = this.questionsList.slice(index)

				this.orderedQuestionsList = [...afterIndex, ...beforeIndex]
				// TODO if we decide on pagination we can fetch new question here
			},
			onVerify(questionId) {
				this.resolveQuestion(questionId)
				// TODO record answer in DB
			},
			debouncedFetchMatchingQuestions: _.debounce(function() {
				this.fetchMatchingQuestions(this.activeFilters)
			}, 500)
		},
		mounted() {
			Promise.all([this.fetchQuestions(), this.fetchDynamicFilters()])
				.then(() => {
					this.fetchQuestionData(this.highlightedQuestion.id)
				})
		},
		watch: {
			highlightedQuestion() {
				if (!this.highlightedQuestion.answers) {
					// TODO loading state
					this.fetchQuestionData(this.highlightedQuestion.id)
				}
			}
		}
	}
</script>
