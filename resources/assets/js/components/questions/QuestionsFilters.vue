<template>
	<div class="wnl-questions-filters">
		<div style="display: flex; align-items: center; justify-content: space-between;">
			<span class="metadata margin vertical">{{$t('questions.filters.heading')}}</span>
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
	.wnl-questions-filters
		width: 100%
</style>

<script>
	import {uniq} from 'lodash'
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
			activeFilters: {
				type: Array,
				required: true,
			},
			filters: {
				type: Object,
				required: true,
			},
		},
		computed: {
			...mapGetters(['isChatMounted', 'isChatVisible', 'isMobile']),
			accordionConfig() {
				return {
					expanded: this.expandedItems,
					flattened: ['resolution'],
					isMobile: this.isMobile,
					selectedElements: this.activeFilters,
				}
			},
			activeParents() {
				return this.activeFilters.map(this.getParents).reduce((a, b) => a.concat(b))
			},
			expandedItems() {
				return uniq(['subjects'].concat(this.activeParents))
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
			onItemToggled({path, selected}) {
				this.$emit('activeFiltersChanged', {filter: path, active: selected})
			},
		},
	}
</script>
