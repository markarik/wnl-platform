<template>
	<div>
		<h3>Filtry</h3>
		<div v-for="(filterGroup, key) in Object.keys(filters)" :key="key">
			<h4>{{filterGroup}}</h4>
			<div v-for="(filter, index) in filters[filterGroup].items" :key="index">
				<input type="checkbox" :name="`${filterGroup}.${filter.value}`" :value="`${filterGroup}.${filter.value}`" v-model="activeFilters">
				<label :for="`${filterGroup}.${filter.value}`">{{filter.name}}</label>
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
		methods: {
			...mapActions('questions', ['fetchMatchingQuestions']),
			debouncedFetchMatchingQuestions: _.debounce(function() {
				this.fetchMatchingQuestions(this.activeFilters)
			}, 500)

		},
		watch: {
			activeFilters() {
				this.debouncedFetchMatchingQuestions()
			},
		}
	}
</script>
