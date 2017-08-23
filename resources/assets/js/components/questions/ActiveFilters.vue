<template>
	<div class="active-filters">
		<div class="filtering-result">
			{{$t('questions.filters.filteringResult')}}
			<span class="matched-count">{{matchedCount}}</span>
			<span class="total-count">
				{{$t('questions.filters.filteringResultFrom', {totalCount})}}
			</span>
		</div>
		<div class="active-filters-heading">
			<span class="metadata">
				{{$t('questions.filters.activeHeading')}}
			</span>
			<span class="active-filters-heading-slot">
				<slot name="heading"/>
			</span>
		</div>
		<div v-if="this.activeFilters.length > 0" class="active-filters-list">
			<span v-for="(filter, index) in activeFiltersObjects"
				class="tag is-success"
				:key="index"
			>
				{{filter.name}}
				<button class="delete is-tiny" @click="removeFilter(filter.path)"></button>
			</span>
		</div>
		<div v-else class="active-filters-list">
			<div class="filters-group">
				<span class="tag">
					{{$t('questions.filters.allQuestions')}}
				</span>
			</div>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.active-filters
		background-color: $color-background-lighter-gray
		padding: $margin-medium $margin-base

	.filtering-result
		font-size: $font-size-minus-1
		margin-top: $margin-tiny

		.matched-count
			color: $color-green
			font-weight: $font-weight-bold

		.total-count
			color: $color-gray-dimmed

	.active-filters-heading
		align-items: center
		display: flex
		justify-content: space-between
		margin-top: $margin-small

	.active-filters-list
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
			margin: $margin-tiny $margin-small $margin-tiny 0

			.delete
				margin-right: -0.7em

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
		computed: {
			activeFiltersObjects() {
				return this.activeFilters.map(filter => ({path: filter, ...this.getFilter(filter)}))
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
		},
	}
</script>
