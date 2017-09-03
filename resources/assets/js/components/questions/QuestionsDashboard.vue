<template>
	<div class="wnl-app-layout">
		<wnl-questions-navigation/>
		<div class="wnl-middle wnl-app-layout-main">
			<div v-if="!id" class="scrollable-main-container">
				<div class="questions-header">
					<div class="questions-breadcrumbs">
						<div class="breadcrumb">
							<span class="icon is-small"><i class="fa fa-check-square-o"></i></span>
						</div>
						<div class="breadcrumb">
							<span class="icon is-small"><i class="fa fa-angle-right"></i></span>
							<span>{{$t('questions.nav.dashboard')}}</span>
						</div>
					</div>
				</div>
				<router-link v-if="planRoute" class="button is-outlined is-small" :to="planRoute">
					Krok zgodny z planem
				</router-link>
			</div>
			<router-view v-else :id="id"/>
		</div>
		<wnl-sidenav-slot
			:isDetached="!isChatMounted"
			:isVisible="isLargeDesktop"
			:hasChat="true"
		>
			<wnl-questions-feed/>
		</wnl-sidenav-slot>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

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
</style>

<script>
	import {isEmpty} from 'lodash'
	import {mapActions, mapGetters} from 'vuex'

	import QuestionsNavigation from 'js/components/questions/QuestionsNavigation'
	import QuestionsFeed from 'js/components/notifications/feeds/questions/QuestionsFeed'
	import SidenavSlot from 'js/components/global/SidenavSlot'

	export default {
		name: 'QuestionsDashboard',
		components: {
			'wnl-questions-navigation': QuestionsNavigation,
			'wnl-questions-feed': QuestionsFeed,
			'wnl-sidenav-slot': SidenavSlot,
		},
		props: {
			id: {
				default: 0,
				type: Number|String,
			}
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
