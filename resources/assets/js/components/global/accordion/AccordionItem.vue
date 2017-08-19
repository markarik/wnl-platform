<template>
	<div class="wnl-accordion-item-container" :class="{
		'is-expaned': expanded,
		'is-flat': flattened,
	}">
		<div v-if="!flattened" class="wnl-accordion-item" :class="{
			'has-children': hasChildren,
			'is-first-level': isFirstLevel,
			'is-selected': selected,
			'is-selectable': isSelectable,
		}">
			<div v-if="isSelectable" class="wai-checkbox" @click="toggleSelected">
				<span class="icon is-small">
					<i class="fa" :class="[selected ? 'fa-check-square-o' : 'fa-square-o']"></i>
				</span>
			</div>
			<div class="wai-content" @click="onItemClick">
				{{content}}
			</div>
			<div v-if="hasChildren" class="wai-expand-icon" @click="toggleExpanded">
				<span class="icon is-small">
					<i class="fa fa-angle-down" :class="[expanded ? 'fa-rotate-180' : '']"></i>
				</span>
			</div>
		</div>
		<div v-if="hasChildren" v-show="expanded || flattened"
			class="wnl-accordion-item-children" :class="{'is-first-level': isFirstLevel}">
			<AccordionItem
				v-for="(childItem, index) in item.items"
				:config="config"
				:item="childItem"
				:key="index"
				:path="`${path}.items[${index}]`"
				@itemToggled="onChildItemToggled"
			/>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-accordion-item-container
		width: 100%

		&.is-flat
			.wnl-accordion-item
				padding-left: 0

	.wnl-accordion-item
		display: flex
		padding: $margin-small 0 $margin-small $margin-base
		user-select: none
		width: 100%

		&:hover
			background: $color-background-lighter-gray

		&.is-selected
			background-color: $color-green

		&.is-first-level
			background: $color-background-light-gray
			font-weight: $font-weight-bold
			padding-left: 0

		&.has-children,
		&.is-selectable
			cursor: pointer

		.wai-checkbox
			width: 1.5em

		.wai-content
			flex: 1 auto

		.wai-expand-icon i
			transition: all $transition-length-base

	.wnl-accordion-item-children
		padding-left: $margin-base

		&.is-first-level
			padding-left: 0
</style>

<script>
	import {size} from 'lodash'

	export default {
		name: 'AccordionItem',
		props: {
			config: {
				required: true,
				type: Object,
			},
			isFirstLevel: {
				default: false,
				type: Boolean,
			},
			item: {
				required: true,
				type: Object,
			},
			path: {
				required: true,
				type: String,
			},
		},
		data() {
			return {
				expanded: false,
				flattened: false,
				selected: false,
			}
		},
		computed: {
			hasChildren() {
				return !!this.item.items
			},
			isSelectable() {
				return !!this.item.value
			},
			content() {
				return this.item.name || this.path
			},
		},
		methods: {
			isExpanded(path) {
				return this.config.expanded.indexOf(path) > -1
			},
			isFlattened(path) {
				return this.config.flattened.indexOf(path) > -1
			},
			onItemClick() {
				if (this.isSelectable) {
					this.toggleSelected()
				} else {
					this.toggleExpanded()
				}
			},
			toggleExpanded() {
				this.expanded = !this.expanded
			},
			toggleSelected() {
				this.selected = !this.selected

				this.$emit('itemToggled', {
					path: this.path,
					selected: this.selected,
				})
			},
			onChildItemToggled(payload) {
				this.$emit('itemToggled', payload)
			},
		},
		mounted() {
			this.expanded = this.isExpanded(this.path)
			this.flattened = this.isFlattened(this.path)
		},
	}
</script>
