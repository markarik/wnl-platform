<template>
	<div>
		<h3>Aktywne filtry</h3>
		<p v-if="activeFiltersNames.length > 0" v-text="activeFiltersNames.join(', ')"></p>
		<p v-else>Wyświetlam wszystkie pytania!</p>
		<p v-if="totalCount">
			{{matchedCount || 0}} / {{totalCount}}
		</p>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>

</style>

<script>
	import {cloneDeep, get} from 'lodash'

	export default {
		name: 'ActiveFilters',
		props: {
			activeFilters: {
				required: true,
				type: Array,
			},
			filters: {
				required: true,
				type: Object,
			},
			matchedCount: {
				type: Number,
			},
			totalCount: {
				type: Number,
			},
		},
		computed: {
			activeFiltersNames() {
				return this.activeFilters.map(filter => this.getFilter(filter).name)
			},
			activeFiltersGrouped() {
				let groupedFilters = cloneDeep(this.filtersGroups)

				this.activeFilters.forEach(filter => {
					const group = filter.split('.')[0]
					groupedFilters[group].push(this.getFilter(filter).name)
				})

				return groupedFilters
			},
			filtersGroups() {
				return Object.keys(this.filters).reduce((result, element) => {
					result[element] = []
					return result
				}, {})
			},
		},
		methods: {
			getFilter(filter) {
				return get(this.filters, filter)
			},
		},
	}
</script>
