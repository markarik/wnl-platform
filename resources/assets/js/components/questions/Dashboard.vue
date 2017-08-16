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
				<div>
					<div v-for="(filterGroup, key) in Object.keys(filters)" :key="key">
						<div v-for="(filter, key) in Object.keys(filters[filterGroup])" :key="key">
							<input type="checkbox" :name="filter" :value="`${filterGroup}.${filter}`" v-model="activeFilters">
							<label :for="filter">{{filters[filterGroup][filter].name}}</label>
						</div>
					</div>
				</div>
				<wnl-quiz-widget
					v-if="questionsList.length > 0"
					:questions="questionsList"
					@changeQuestion="performChangeQuestion"
					@verify="onVerify"
					@selectAnswer="selectAnswer"
				></wnl-quiz-widget>
			</div>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-app-layout-main
		width: 100%
		max-width: initial

</style>

<script>
	import { mapGetters, mapActions } from 'vuex'

	import Sidenav from 'js/components/global/Sidenav'
	import SidenavSlot from 'js/components/global/SidenavSlot'
	import MainNav from 'js/components/MainNav'
	import QuizWidget from 'js/components/quiz/QuizWidget'

	export default {
		components: {
			'wnl-sidenav': Sidenav,
			'wnl-sidenav-slot': SidenavSlot,
			'wnl-main-nav': MainNav,
			'wnl-quiz-widget': QuizWidget,
		},
		data() {
			return {
				activeFilters: [],
				orderedQuestionsList: []
			}
		},
		computed: {
			...mapGetters(['isSidenavMounted', 'isSidenavVisible']),
			...mapGetters('questions', ['questions', 'filters']),
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
			}
		},
		mounted() {
			Promise.all([this.fetchQuestions(), this.fetchDynamicFilters()])
				.then(() => {
					this.fetchQuestionData(this.highlightedQuestion.id)
				})
		},
		watch: {
			activeFilters() {
				//TODO watch active filters and issue request for matching ids
			},
			highlightedQuestion() {
				if (!this.highlightedQuestion.answers) {
					// TODO loading state
					this.fetchQuestionData(this.highlightedQuestion.id)
				}
			}
		}
	}
</script>
