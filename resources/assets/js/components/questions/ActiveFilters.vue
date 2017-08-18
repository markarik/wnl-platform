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
	import {get} from 'lodash'

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
				return this.activeFilters.map(filter => {
					return get(this.filters, filter).name
				})
			},
		},
	}
</script>
