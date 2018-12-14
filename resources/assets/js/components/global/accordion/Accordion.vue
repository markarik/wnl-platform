<template>
	<div class="wnl-accordion" v-if="hasItems">
		<wnl-accordion-item
			v-for="(item, path) in dataSource"
			:config="configWithDefaults"
			:item="item"
			:key="path"
			:level="0"
			:loading="loading"
			:path="path"
			@itemToggled="onItemToggled"
		/>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>

</style>

<script>
import {size} from 'lodash';
import AccordionItem from 'js/components/global/accordion/AccordionItem';

const defaultConfig = {
	disableEmpty: false,
	expanded: [],
	flattened: [],
	isMobile: false,
	itemsNamesSource: '',
	selectedElements: [],
	showCounts: true
};

export default {
	name: 'Accordion',
	components: {
		'wnl-accordion-item': AccordionItem,
	},
	props: {
		config: {
			default: defaultConfig,
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
			return size(this.dataSource) > 0;
		},
		configWithDefaults() {
			return {
				...defaultConfig, ...this.config
			};
		}
	},
	methods: {
		onItemToggled(payload) {
			this.$emit('itemToggled', payload);
		}
	},
};
</script>
