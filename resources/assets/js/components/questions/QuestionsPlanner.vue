<template>
	<div>
		<input name="startDate" :v-bind="startDate" type="date"/>
		<label for="startDate">Kiedy zaczynasz?</label>
		<input name="endDate" :v-bind="endDate" type="date"/>
		<label for="endDate">Kiedy kończysz?</label>
		<input name="slackDays" :v-bind="slackDays" type="number"/>
		<label for="slackDays">Ile dni wolnych?</label>

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
				'getReaction',
				'questionsList',
			]),
		},
		methods: {
			...mapActions('questions', [
				'fetchQuestionsCount',
				'fetchDynamicFilters',
				'activeFiltersToggle'
			]),
			onActiveFiltersChanged(payload) {
				this.activeFiltersToggle(payload)
			},
		},
		mounted() {
			Promise.all([this.fetchDynamicFilters(), this.fetchQuestionsCount()])
		},
	}
</script>
