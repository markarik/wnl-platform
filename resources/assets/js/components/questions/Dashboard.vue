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
				<wnl-quiz-widget
				:questions="getQuestionsList"
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
		computed: {
			...mapGetters(['isSidenavMounted', 'isSidenavVisible']),
			...mapGetters('questions', ['getQuestionsList'])
		},
		methods: {
			...mapActions('questions', ['fetchQuestions']),
			performChangeQuestion() {

			},
			resolveQuestion() {

			}
		},
		mounted() {
			this.fetchQuestions()
		}
	}
</script>
