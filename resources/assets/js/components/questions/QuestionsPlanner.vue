<template>
	<div>
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
</template>

<script>
	import {mapActions, mapGetters} from 'vuex'

	import QuestionsFilters from 'js/components/questions/QuestionsFilters'

	export default {
		name: 'QuestionsPlanner',
		components: {
			'wnl-questions-filters': QuestionsFilters
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
				console.log(this.startDate, '...startDate')
				console.log(this.endDate, '...endDate')
				console.log(this.activeFilters, '...activeFilters')
				console.log(this.slackDays, '...slackDays')
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
