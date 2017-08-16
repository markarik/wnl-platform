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
					:questions="questionsList"
					@changeQuestion="performChangeQuestion"
					@verify="resolveQuestion"
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
				activeFilters: []
			}
		},
		computed: {
			...mapGetters(['isSidenavMounted', 'isSidenavVisible']),
			...mapGetters('questions', ['questionsList', 'filters']),
			highlightedQuestion() {
				return this.questionsList[0]
			},
		},
		methods: {
			...mapActions('questions', ['fetchQuestions', 'fetchQuestionAnswers', 'fetchDynamicFilters']),
			performChangeQuestion() {

			},
			resolveQuestion() {

			}
		},
		mounted() {
			Promise.all([this.fetchQuestions(), this.fetchDynamicFilters()])
				.then(() => {
					this.fetchQuestionAnswers(this.highlightedQuestion.id)
				})
		},
	}
</script>
