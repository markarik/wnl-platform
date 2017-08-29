<template>
	<div class="wnl-accordion-item-container" :class="{
		'is-expanded': expanded,
		'is-flat': flattened,
		'is-mobile': config.isMobile,
	}">
		<div v-if="!flattened" class="wnl-accordion-item" :class="{
			'has-children': hasChildren,
			'is-first-level': isFirstLevel,
			'is-selected': isSelected,
			'is-selectable': isSelectable,
		}" @click="onItemClick">
			<div v-if="isSelectable" class="wai-checkbox">
				<span class="icon is-small">
					<i class="fa" :class="[isSelected ? 'fa-check-square-o' : 'fa-square-o']"></i>
				</span>
			</div>
			<div class="wai-content">
				{{content}} ({{ item.count }})
			</div>
			<div v-if="hasChildren" class="wai-expand-icon" ref="expand">
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

	$toggle-icon-size: 2 * $margin-base
	$highlight-color: $color-background-lighter-gray

	.is-mobile
		.wnl-accordion-item:not(.is-selected):not(.is-first-level):hover
				background-color: initial

	.wnl-accordion-item-container
		width: 100%

		&.is-expanded
			border-bottom: $border-light-gray
			padding-bottom: $margin-base

	.wnl-accordion-item
		align-items: center
		display: flex
		font-size: $font-size-minus-1
		line-height: $line-height-minus
		padding: $margin-small $margin-small $margin-small $margin-base
		user-select: none
		width: 100%
		height: $toggle-icon-size + 2 * $margin-small

		&:hover
			background: $color-background-lighter-gray

		&.is-selected
			background-color: $highlight-color

		&.is-first-level
			background: $color-background-light-gray
			font-size: $font-size-minus-1
			letter-spacing: 1px
			padding: $margin-medium $margin-small $margin-medium $margin-medium
			text-transform: uppercase

			.wai-expand-icon
				border-color: transparent

		&.has-children,
		&.is-selectable
			cursor: pointer

		.wai-checkbox
			width: 1.5em

			i
				font-size: $font-size-minus-1

		.wai-content
			flex: 1 auto

		.wai-expand-icon
			align-items: center
			border: $border-light-gray
			border-radius: $border-radius-small
			display: flex
			height: $toggle-icon-size
			justify-content: center
			width: $toggle-icon-size

			i
				transition: all $transition-length-base

	.wnl-accordion-item-children
		padding-left: $margin-big

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
			isSelected() {
				if (this.config.hasOwnProperty('selectedElements')) {
					return this.config.selectedElements.indexOf(this.path) > -1
				}

				return this.selected
			},
			content() {
				return this.item.name || this.$t(`${this.config.itemsNamesSource}.${this.path}`)
			},
		},
		methods: {
			isExpanded(path) {
				return this.config.expanded.indexOf(path) > -1
			},
			isFlattened(path) {
				return this.config.flattened.indexOf(path) > -1
			},
			onItemClick(event) {
				if (this.isSelectable && event.path.indexOf(this.$refs.expand) === -1) {
					this.toggleSelected()
				} else {
					this.toggleExpanded()
				}
			},
			toggleExpanded() {
				this.expanded = !this.expanded
			},
			toggleSelected() {
				this.selected = !this.isSelected
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
