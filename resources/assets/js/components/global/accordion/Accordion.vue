<template>
	<div class="wnl-accordion" v-if="hasItems">
		<wnl-accordion-item
			v-for="(item, path) in dataSource"
			:config="config"
			:isFirstLevel="true"
			:item="item"
			:key="path"
			:path="path"
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
			dataSource: {
				required: true,
				type: Object,
			},
			config: {
				default: {
					expanded: [],
					flattened: [],
					isMobile: false,
				},
				type: Object,
			},
		},
		computed: {
			hasItems() {
				return size(this.dataSource) > 0
			}
		},
	}
</script>
