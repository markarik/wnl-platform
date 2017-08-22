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
		<div class="filtering-result">
			{{$t('questions.filters.filteringResult')}}
			{{$t('questions.filters.filteringResultNumbers', {matchedCount, totalCount})}}
		</div>
		<div v-if="this.activeFilters.length > 0" class="active-filters-list">
			<div class="filters-group" :class="group" v-for="(filters, group) in activeFiltersGrouped" v-if="filters.length > 0" :key="group">
				<span class="filters-group-heading">{{$t(`questions.filters.items.${group}`)}}:</span>
				<span v-for="(filter, index) in filters"
					class="tag"
					:class="{'is-success': hasChanges}"
					:key="index"
				>
					{{filter.name}}
					<button class="delete is-tiny" @click="removeFilter(filter.path)"></button>
				</span>
			</div>
		</div>
		<div v-else class="active-filters-list">
			<div class="filters-group">
				<span class="tag" :class="{'is-success': hasChanges}">
					{{$t('questions.filters.allQuestions')}}
				</span>
			</div>
		</div>
		<div v-if="hasChanges || loading" class="has-text-centered margin top">
			<a class="button is-small is-success" :class="{'is-loading': loading}" @click="onSubmit">
				{{$t('questions.filters.submit')}}
			</a>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.active-filters
		background-color: $color-background-lighter-gray
		border-radius: $border-radius-small
		padding: $margin-medium $margin-base

	.active-filters-heading
		align-items: center
		display: flex
		justify-content: space-between

	.active-filters-list
		margin-top: $margin-small
		width: 100%

		.filters-group
			align-items: center
			display: flex
			margin-bottom: $margin-small

			&:last-of-type
				margin-bottom: 0

			.filters-group-heading
				font-size: $font-size-minus-2
				letter-spacing: 1px
				text-transform: uppercase

		.tag
			margin: $margin-tiny

			.delete
				margin-right: -0.7em

	.filtering-result
		font-size: $font-size-minus-2
		margin-top: -$margin-small

	.tag:not(.is-success)
		background-color: $color-background-light-gray
</style>

<script>
	import {cloneDeep, isEqual, get} from 'lodash'

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
			loading: {
				default: false,
				type: Boolean,
			},
			matchedCount: {
				type: Number,
			},
			totalCount: {
				type: Number,
			},
		},
		data() {
			return {
				appliedFilters: [],
			}
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
			hasAppliedFilters() {
				return this.appliedFilters.length > 0
			},
			hasChanges() {
				return !isEqual(this.appliedFilters, this.activeFilters)
			},
		},
		methods: {
			getFilter(filter) {
				return get(this.filters, filter)
			},
			onSubmit() {
				this.updateAppliedFilters()
				this.$emit('fetchMatchingQuestions')
			},
			removeFilter(filter) {
				this.$emit('activeFiltersChanged', {filter, active: false})
			},
			updateAppliedFilters() {
				this.appliedFilters = _.cloneDeep(this.activeFilters)
			},
		},
		mounted() {
			this.updateAppliedFilters()
		},
	}
</script>
