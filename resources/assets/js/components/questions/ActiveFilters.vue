<template>
	<div>
		<h3>Aktywne filtry</h3>
		<p v-if="activeFilters.length > 0" v-text="activeFilters.join(', ')"></p>
		<p v-else>Wyświetlam wszystkie pytania!</p>
		<p v-if="matchedCount && totalCount">
			{{matchedCount}} / {{totalCount}}
		</p>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>

</style>

<script>
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
	}
</script>
