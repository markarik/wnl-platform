<template>
	<div class="wnl-app-layout">
		<wnl-questions-navigation/>
		<div class="wnl-middle wnl-app-layout-main">
			<div class="scrollable-main-container">
				<p class="title is-3">Dashboard</p>
				<router-link v-if="planRoute" class="button is-outlined is-small" :to="planRoute">
					Krok zgodny z planem
				</router-link>
			</div>
		</div>
		<wnl-sidenav-slot
			:isDetached="!isChatMounted"
			:isVisible="isLargeDesktop"
			:hasChat="true"
		>
		</wnl-sidenav-slot>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>

</style>

<script>
	import {isEmpty} from 'lodash'
	import {mapActions, mapGetters} from 'vuex'

	import QuestionsNavigation from 'js/components/questions/QuestionsNavigation'
	import SidenavSlot from 'js/components/global/SidenavSlot'

	export default {
		name: 'QuestionsDashboard',
		components: {
			'wnl-questions-navigation': QuestionsNavigation,
			'wnl-sidenav-slot': SidenavSlot,
		},
		data() {
			return {
				planRoute: {},
			}
		},
		computed: {
			...mapGetters(['currentUserId','isChatMounted', 'isLargeDesktop']),
			...mapGetters('questions', ['filters']),
		},
		methods: {
			...mapActions('questions', ['fetchDynamicFilters']),
			setPlanRoute() {
				console.log(this.filters)
				this.planRoute = {
					name: 'questions-list',
					params: {
						presetFilters: [
							'quiz-planned.items[0]',
							'quiz-resolution.items[0]',
						],
					},
				}
			}
		},
		mounted() {
			isEmpty(this.filters)
				? this.fetchDynamicFilters().then(this.setPlanRoute)
				: this.setPlanRoute()
		},
	}
</script>
