<template>
	<div class="active-filters">
		<div class="active-filters-heading">
			<span class="metadata">
				{{$t('questions.filters.activeHeading')}}
			</span>
			<span class="active-filters-heading-slot">
				<slot name="heading"/>
			</span>
		</div>
		<div v-if="activeFiltersNames.length > 0">
			<p class="filters-group" :class="group" v-for="(filters, group) in activeFiltersGrouped" v-if="filters.length > 0" :key="group">
				{{group}}: <span class="tag" v-for="(filter, index) in filters" :key="index">
					{{filter.name}}
					<button class="delete is-tiny" @click="removeFilter(filter.path)"></button>
				</span>
			</p>
			<p class="filtering-result" v-if="totalCount">
				{{$t('questions.filters.filteringResult', {matchedCount, totalCount})}}
			</p>
		</div>
		<p class="filtering-result" v-else>{{$t('questions.filters.allQuestions', {totalCount})}}</p>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.active-filters
		background-color: $color-background-lighter-gray
		// border: $border-light-gray
		border-radius: $border-radius-small
		padding: $margin-medium $margin-base

	.active-filters-heading
		align-items: center
		display: flex
		justify-content: space-between
		margin-bottom: $margin-small

	.filtering-result
		font-size: $font-size-minus-2
		letter-spacing: 1px
		text-transform: uppercase
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
