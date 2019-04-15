<template>
	<div class="wnl-accordion-item-container" :class="{
		'is-expanded': expanded,
		'is-flat': flattened,
		'is-mobile': config.isMobile,
	}">
		<div v-if="!flattened" class="wnl-accordion-item" :class="[`level-${level}`, {
			'has-children': hasChildren,
			'is-disabled': isDisabled,
			'is-selected': isSelected,
			'is-selectable': isSelectable,
			'loading': loading,
		}]" @click="onItemClick">
			<div v-if="isSelectable" class="wai-checkbox">
				<span class="icon is-small">
					<i class="fa" :class="[isSelected ? 'fa-check-square-o' : 'fa-square-o']"></i>
				</span>
			</div>
			<div class="wai-content">
				<span class="text">{{content}}</span>
				<span class="count" v-if="!loading && count !== false">{{`(${count})`}}</span>
				<span class="loader" v-if="loading"></span>
			</div>
			<div v-if="hasChildren" class="wai-expand-icon" @click.stop="toggleExpanded">
				<span class="icon is-small">
					<i class="fa fa-angle-down" :class="[expanded ? 'fa-rotate-180' : '']"></i>
				</span>
			</div>
		</div>
		<div v-if="hasChildren" v-show="expanded || flattened"
			class="wnl-accordion-item-children" :class="[`level-${level}`]">
			<accordion-item
				v-for="(childItem, index) in item.items"
				:config="config"
				:item="childItem"
				:key="index"
				:level="level + 1"
				:loading="loading"
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
		.wnl-accordion-item:not(.is-selected):not(.level-0):hover
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

		&.is-disabled
			color: $color-inactive-gray
			cursor: default

			&:hover
				background: initial

			&.has-children, &.is-selectable
				cursor: default

			.wai-content
				.count
					color: $color-inactive-gray

		&.is-selected
			background-color: $highlight-color

		&.level-0
			background: $color-background-light-gray
			border: 0
			font-size: $font-size-minus-1
			letter-spacing: 1px
			padding: $margin-medium $margin-small $margin-medium $margin-medium
			text-transform: uppercase

			&:hover
				background: $color-background-light-gray

			.wai-expand-icon
				border-color: transparent

		&.level-2
			border-left: $border-light-gray

		&.has-children,
		&.is-selectable
			cursor: pointer

		&.loading
			cursor: wait

		.wai-checkbox
			width: 1.5em

			i
				font-size: $font-size-minus-1

		.wai-content
			align-items: center
			display: flex
			flex: 1 auto
			justify-content: flex-start

			.loader
				margin-left: $margin-small

			.count
				align-self: flex-end
				color: $color-background-gray
				font-size: $font-size-minus-3
				font-weight: $font-weight-bold
				margin-left: $margin-small

		.wai-expand-icon
			align-items: center
			display: flex
			height: $toggle-icon-size
			justify-content: center
			margin-left: $margin-small
			width: $toggle-icon-size

			.icon
				border: $border-light-gray
				border-radius: $border-radius-small
				height: $toggle-icon-size
				width: $toggle-icon-size

			i
				transition: all $transition-length-base

	.wnl-accordion-item-children
		padding-left: $margin-big

		&.level-0
			padding-left: 0
</style>

<script>
export default {
	name: 'AccordionItem',
	props: {
		config: {
			required: true,
			type: Object,
		},
		item: {
			required: true,
			type: Object,
		},
		level: {
			default: 0,
			type: Number,
		},
		loading: {
			default: false,
			type: Boolean,
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
		};
	},
	computed: {
		content() {
			if (this.item.hasOwnProperty('name')) return this.item.name;

			const messageKey = this.item.hasOwnProperty('message') ? this.item.message : this.item.value;

			return this.$t(`${this.config.itemsNamesSource}.${messageKey}`);
		},
		count() {
			if (!this.config.showCounts) return false;
			if (!this.item.hasOwnProperty('count')) return false;

			return this.item.count || 0;
		},
		hasChildren() {
			return !!this.item.items;
		},
		isDisabled() {
			return this.config.disableEmpty && this.count === 0;
		},
		isSelectable() {
			return !!this.item.value;
		},
		isSelected() {
			if (this.config.hasOwnProperty('selectedElements')) {
				return this.config.selectedElements.indexOf(this.path) > -1;
			}

			return this.selected;
		},
	},
	methods: {
		isExpanded(path) {
			return this.config.expanded.indexOf(path) > -1;
		},
		isFlattened(path) {
			return this.config.flattened.indexOf(path) > -1;
		},
		onChildItemToggled(payload) {
			this.$emit('itemToggled', payload);
		},
		onItemClick() {
			if (this.isDisabled || this.loading) return false;

			if (this.isSelectable) {
				this.toggleSelected();
			} else {
				this.toggleExpanded();
			}
		},
		toggleExpanded() {
			this.expanded = !this.expanded;
		},
		toggleSelected() {
			this.selected = !this.isSelected;
			this.$emit('itemToggled', {
				path: this.path,
				selected: this.selected,
			});
		},
	},
	mounted() {
		this.expanded = this.isExpanded(this.path);
		this.flattened = this.isFlattened(this.path);
	},
};
</script>
