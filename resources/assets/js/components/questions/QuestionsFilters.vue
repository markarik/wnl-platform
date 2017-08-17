<template>
	<div>
		<h3>Filtry</h3>
		<div v-for="(filterGroup, key) in Object.keys(filters)" :key="key">
			<h4>{{filterGroup}}</h4>
			<div v-for="(filter, key) in Object.keys(filters[filterGroup].items)" :key="key">
				<input type="checkbox" :name="filter" :value="`${filterGroup}.${filter}`" v-model="activeFilters">
				<label :for="filter">{{filters[filterGroup].items[filter].name}}</label>
			</div>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>

</style>

<script>
	import {mapActions, mapGetters} from 'vuex'

	export default {
		name: 'QuestionsFilters',
		data() {
			return {
				activeFilters: [],
			}
		},
		computed: {
			...mapGetters('questions', ['filters']),
		},
		watch: {
			activeFilters() {
				//TODO watch active filters and issue request for matching ids
			},
		}
	}
</script>
