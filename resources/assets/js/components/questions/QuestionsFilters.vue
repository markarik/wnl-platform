<template>
	<div class="wnl-questions-filters">
		<h3>Filtry</h3>
		<wnl-accordion
			:dataSource="filters"
			:config="accordionConfig"
		/>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	.wnl-questions-filters
		width: 100%
</style>

<script>
	import {mapActions, mapGetters} from 'vuex'

	import Accordion from 'js/components/global/accordion/Accordion'

	const config = {
		flattened: ['resolution'],
		expanded: ['subjects'],
	}

	export default {
		name: 'QuestionsFilters',
		components: {
			'wnl-accordion': Accordion,
		},
		props: {
			filters: {
				type: Object,
				required: true,
			},
		},
		data() {
			return {
				activeFilters: [],
			}
		},
		computed: {
			...mapGetters(['isMobile']),
			accordionConfig() {
				return {
					expanded: ['subjects'],
					flattened: ['resolution'],
					isMobile: this.isMobile,
				}
			},
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
