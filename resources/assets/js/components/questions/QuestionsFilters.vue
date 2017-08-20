<template>
	<div class="wnl-questions-filters">
		<div style="display: flex; align-items: center; justify-content: space-between;">
			<span class="metadata margin vertical">Filtry</span>
			<a v-if="!isChatMounted && isChatVisible" @click="toggleChat">Ukryj filtry</a>
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
					expanded: ['subjects'],
					flattened: ['resolution'],
					isMobile: this.isMobile,
					selectedElements: this.activeFilters,
				}
			},
		},
		methods: {
			...mapActions(['toggleChat']),
			onItemToggled({path, selected}) {
				this.$emit('activeFiltersChanged', {filter: path, active: selected})
			},
		},
	}
</script>
