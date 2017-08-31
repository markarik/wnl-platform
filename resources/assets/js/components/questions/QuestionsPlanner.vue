<template>
	<div class="wnl-app-layout">
		<wnl-questions-navigation/>
		<div class="wnl-middle wnl-app-layout-main" :class="{'is-full-width': isLargeDesktop}">
			<div class="scrollable-main-container">
				<div class="questions-header">
					<div class="questions-breadcrumbs">
						<div class="breadcrumb">
							<span class="icon is-small"><i class="fa fa-check-square-o"></i></span>
						</div>
						<div class="breadcrumb">
							<span class="icon is-small"><i class="fa fa-angle-right"></i></span>
							<span>{{$t('questions.nav.planner')}}</span>
						</div>
					</div>
					<a v-if="isMobile" slot="heading" class="mobile-show-active-filters">
						<span>{{$t('questions.filters.show')}}</span>
						<span class="icon is-tiny">
							<i class="fa fa-sliders"></i>
						</span>
					</a>
				</div>
				<label for="startDate">Kiedy zaczynasz?</label>
				<input name="startDate" v-model="startDate" type="date"/>
				<label for="endDate">Kiedy kończysz?</label>
				<input name="endDate" v-model="endDate" type="date"/>
				<label for="slackDays">Ile dni wolnych?</label>
				<input name="slackDays" v-model="slackDays" type="number"/>

				<p><button @click="createPlan">Ułóż plan!</button></p>
				<p>Pytania z których kategorii Ciebie interesują?</p>
			</div>
		</div>
		<wnl-sidenav-slot
			:isDetached="!isChatMounted"
			:isVisible="isLargeDesktop"
			:hasChat="true"
		>
			<wnl-questions-filters
				:activeFilters="activeFilters"
				:fetchingQuestions="false"
				:filters="filters"
				@activeFiltersChanged="onActiveFiltersChanged"
			/>
		</wnl-sidenav-slot>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
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

	.wnl-app-layout-main.is-full-width
		max-width: 100%
</style>

<script>
	import {mapActions, mapGetters} from 'vuex'

	import QuestionsFilters from 'js/components/questions/QuestionsFilters'
	import QuestionsNavigation from 'js/components/questions/QuestionsNavigation'
	import SidenavSlot from 'js/components/global/SidenavSlot'

	export default {
		name: 'QuestionsPlanner',
		components: {
			'wnl-questions-filters': QuestionsFilters,
			'wnl-questions-navigation': QuestionsNavigation,
			'wnl-sidenav-slot': SidenavSlot,
		},
		props: [],
		data() {
			return {
				startDate: new Date(),
				endDate: new Date(),
				slackDays: 0
			}
		},
		computed: {
			...mapGetters(['isChatMounted', 'isChatVisible', 'isLargeDesktop', 'isMobile']),
			...mapGetters('questions', [
				'activeFilters',
				'filters',
			]),
		},
		methods: {
			...mapActions('questions', [
				'activeFiltersToggle',
				'buildPlan',
				'fetchQuestionsCount',
				'fetchDynamicFilters',
			]),
			onActiveFiltersChanged(payload) {
				this.activeFiltersToggle(payload)
			},
			createPlan() {
				this.buildPlan({
					startDate: this.startDate,
					endDate: this.endDate,
					activeFilters: this.activeFilters,
					slackDays: this.slackDays
				})
			}
		},
		mounted() {
			const presetFilters = []

			this.activeFiltersSet()

			Promise.all([
				this.fetchDynamicFilters(),
				this.fetchQuestionsCount()
			])
		},
	}
</script>
