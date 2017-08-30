<template>
	<div class="wnl-app-layout">
		<wnl-questions-navigation/>
		<div class="wnl-middle wnl-app-layout-main">
			<div class="scrollable-main-container">
				<p class="title is-3">Planner</p>
				<label for="startDate">Kiedy zaczynasz?</label>
				<input name="startDate" v-model="startDate" type="date"/>
				<label for="endDate">Kiedy kończysz?</label>
				<input name="endDate" v-model="endDate" type="date"/>
				<label for="slackDays">Ile dni wolnych?</label>
				<input name="slackDays" v-model="slackDays" type="number"/>

				<p><button @click="createPlan">Ułóż plan!</button></p>
				<p>Pytania z których kategorii Ciebie interesują?</p>
				<wnl-questions-filters
					:activeFilters="activeFilters"
					:fetchingQuestions="false"
					:filters="filters"
					@activeFiltersChanged="onActiveFiltersChanged"
				/>
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
			...mapGetters(['isChatMounted', 'isLargeDesktop']),
			...mapGetters('questions', [
				'activeFilters',
				'filters',
			]),
		},
		methods: {
			...mapActions('questions', [
				'fetchQuestionsCount',
				'fetchDynamicFilters',
				'activeFiltersToggle',
				'buildPlan'
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
			Promise.all([this.fetchDynamicFilters(), this.fetchQuestionsCount()])
		},
	}
</script>
