<template>
	<div class="wnl-questions-filters">
		<wnl-active-filters
		:activeFilters="activeFilters"
		:loading="fetchingQuestions"
		:filters="filters"
		:matchedCount="matchedQuestionsCount"
		:totalCount="allQuestionsCount"
		@activeFiltersChanged="onActiveFiltersChanged"
		@fetchMatchingQuestions="$emit('fetchMatchingQuestions')"
		/>
		<div class="filters-heading">
			<span class="metadata margin vertical">
				<span class="icon is-tiny"><i class="fa fa-sliders"></i></span>
				{{$t('questions.filters.heading')}}
			</span>
			<a v-if="!isChatMounted && isChatVisible" @click="toggleChat">{{$t('questions.filters.hide')}}</a>
		</div>
		<wnl-accordion
			:dataSource="filters"
			:config="accordionConfig"
			@itemToggled="onItemToggled"
		/>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-questions-filters
		overflow-y: auto
		width: 100%

	.filters-heading
		align-items: center
		border-bottom: $border-light-gray
		display: flex
		justify-content: space-between
		padding: 0 $margin-base
</style>

<script>
	import {uniq} from 'lodash'
	import {mapActions, mapGetters} from 'vuex'

	import Accordion from 'js/components/global/accordion/Accordion'
	import ActiveFilters from 'js/components/questions/ActiveFilters'

	const config = {
		flattened: ['resolution'],
		expanded: ['subjects'],
	}

	export default {
		name: 'QuestionsFilters',
		components: {
			'wnl-accordion': Accordion,
			'wnl-active-filters': ActiveFilters,
		},
		props: {
			activeFilters: {
				type: Array,
				required: true,
			},
			fetchingQuestions: {
				default: false,
				type: Boolean,
			},
			filters: {
				type: Object,
				required: true,
			},
		},
		computed: {
			...mapGetters(['isChatMounted', 'isChatVisible', 'isMobile']),
			...mapGetters('questions', [
				'allQuestionsCount',
				'matchedQuestionsCount',
			]),
			accordionConfig() {
				return {
					expanded: this.expandedItems,
					flattened: ['planned', 'resolution'],
					isMobile: this.isMobile,
					itemsNamesSource: 'questions.filters.items',
					selectedElements: this.activeFilters,
				}
			},
			activeParents() {
				return this.activeFilters.map(this.getParents).reduce((a, b) => a.concat(b))
			},
			expandedItems() {
				const expanded = ['subjects']
				return this.hasActive ? uniq(expanded.concat(this.activeParents)) : expanded
			},
			hasActive() {
				return this.activeFilters.length > 0
			},
		},
		methods: {
			...mapActions(['toggleChat']),
			getParents(filter) {
				return filter.split('.').map((item, index, splitted) => {
					return splitted.slice(0, index).join('.')
				})
			},
			onActiveFiltersChanged(payload) {
				this.$emit('activeFiltersChanged', payload)
			},
			onItemToggled({path, selected}) {
				this.$emit('activeFiltersChanged', {filter: path, active: selected})
			},
		},
	}
</script>
