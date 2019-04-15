<template>
	<div class="active-filters">
		<div class="refresh">
			<div class="refresh-link">
				<span class="icon is-tiny">
					<i class="fa fa-refresh" :class="{'fa-spin': loading}"></i>
				</span>
				<a @click="$emit('refresh')">{{$t('questions.filters.refresh')}}</a>
			</div>
			<div class="autorefresh control">
				<label for="autorefresh">{{$t('questions.filters.autorefresh')}}</label>
				<input id="autorefresh" type="checkbox" class="checkbox" v-model="autorefresh">
			</div>
		</div>
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
				{{filterDisplayName(filter)}}
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
	@import 'resources/assets/sass/mixins'

	.refresh
		+flex-space-between()

		.refresh-link
			font-size: $font-size-minus-2
			text-transform: uppercase

			.icon
				color: $color-background-gray

		.autorefresh
			align-items: center
			color: $color-gray
			display: flex
			justify-content: flex-end
			font-size: $font-size-minus-2
			text-transform: uppercase

			.checkbox
				margin-left: $margin-small

	.active-filters
		padding: $margin-small $margin-base $margin-medium

	.filtering-result
		font-size: $font-size-minus-1
		margin-top: $margin-tiny

		.matched-count
			color: $color-green
			font-weight: $font-weight-bold

		.total-count
			color: $color-gray

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
</style>

<script>
import { nextTick } from 'vue';
import { get } from 'lodash';

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
		itemsNamesSource: {
			required: true,
			type: String,
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
			autorefresh: true,
			elementHeight: 0,
		};
	},
	computed: {
		activeFiltersObjects() {
			return this.activeFilters.map(filter => ({ path: filter, ...this.getFilter(filter) }));
		},
	},
	methods: {
		filterDisplayName(filter) {
			if (filter.type === 'search') {
				return filter.items[0] && filter.items[0].value && `Fraza: ${filter.items[0].value}`;
			}

			if (filter.hasOwnProperty('name')) return filter.name;

			const messageKey = filter.hasOwnProperty('message') ? filter.message : filter.value;

			return this.$t(`${this.itemsNamesSource}.${messageKey}`);
		},
		getFilter(filter) {
			if (filter.startsWith('search.')) {
				return this.filters['search'];
			}
			return get(this.filters, filter);
		},
		emitHeight() {
			this.$emit('elementHeight', this.$el.offsetHeight);
		},
		removeFilter(filter) {
			this.$emit('activeFiltersChanged', { filter, active: false });
		},
	},
	mounted() {
		this.emitHeight();
	},
	watch: {
		activeFilters() {
			nextTick(this.emitHeight);
		},
		autorefresh(to) {
			this.$emit('autorefreshChange', to);
		},
	}
};
</script>
