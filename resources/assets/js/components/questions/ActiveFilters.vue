<template>
	<div>
		<h3>Aktywne filtry</h3>
		<div v-if="activeFiltersNames.length > 0">
			<p v-for="(filters, group) in activeFiltersGrouped" v-if="filters.length > 0" :key="group">
				{{group}}: <span class="tag" v-for="(filter, index) in filters" :key="index">
					{{filter.name}}
					<button class="delete is-tiny" @click="removeFilter(filter.path)"></button>
				</span>
			</p>
		</div>
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
					groupedFilters[group].push({path: filter, ...this.getFilter(filter)})
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
			removeFilter(filter) {
				this.$emit('activeFiltersChanged', {filter, active: false})
			},
		},
	}
</script>
