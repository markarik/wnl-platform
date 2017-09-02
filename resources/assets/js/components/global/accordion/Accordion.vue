<template>
	<div class="wnl-accordion" v-if="hasItems">
		<wnl-accordion-item
			v-for="(item, path) in dataSource"
			:config="config"
			:isFirstLevel="true"
			:item="item"
			:key="path"
			:loading="loading"
			:path="path"
			@itemToggled="onItemToggled"
		/>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>

</style>

<script>
	import {size} from 'lodash'
	import AccordionItem from 'js/components/global/accordion/AccordionItem'

	export default {
		name: 'Accordion',
		components: {
			'wnl-accordion-item': AccordionItem,
		},
		props: {
			config: {
				default: {
					expanded: [],
					flattened: [],
					isMobile: false,
					itemsNamesSource: '',
					selectedElements: [],
				},
				type: Object,
			},
			dataSource: {
				required: true,
				type: Object,
			},
			loading: {
				default: false,
				type: Boolean,
			},
		},
		computed: {
			hasItems() {
				return size(this.dataSource) > 0
			}
		},
		methods: {
			onItemToggled(payload) {
				this.$emit('itemToggled', payload)
			}
		},
	}
</script>
