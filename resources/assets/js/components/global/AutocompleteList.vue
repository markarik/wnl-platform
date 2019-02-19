<template>
	<div
		class="autocomplete-box"
		:class="{'is-down': isDown}"
		v-show="items.length || $slots.footer"
	>
		<ul
			class="autocomplete-box__list"
			tabindex="-1"
			ref="autocompleteList"
		>
			<li
				class="autocomplete-box__item"
				v-for="(item, index) in items"
				@click="$emit('change', item)"
				:class="{ active: index === activeIndex }"
				:key="index"
			>
				<slot :item="item"></slot>
			</li>
		</ul>
		<slot name="footer"></slot>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.autocomplete-box
		background: $autocomplete-box-background
		border: $autocomplete-box-border
		bottom: 44px
		box-shadow: $autocomplete-box-shadow
		color: $autocomplete-text-color
		left: 0
		max-width: 300px
		position: absolute
		width: 100%
		z-index: $z-index-autocomplete

		&__text
			padding: 5px 10px

		&.is-down
			bottom: auto
			top: 100%

		&:focus
			outline: none

		&__list
			max-height: 50vh
			overflow-y: auto

		&__item
			align-items: center
			cursor: pointer
			display: flex
			font-size: 12px
			font-weight: 900
			padding: 8px 10px
			text-align: left

			&:hover,
			&.active
				background: $autocomplete-active-item-background

		&__text
			padding: 5px 10px
</style>

<script>
import {scrollToY} from 'js/utils/animations';

export default {
	props: {
		items: {
			type: Array,
			default: () => [],
		},
		isDown: {
			type: Boolean,
			default: false,
		},
		activeIndex: {
			type: Number,
			default: -1,
		},
	},
	watch: {
		activeIndex() {
			this.$nextTick(() => {
				const autocompleteList = this.$refs.autocompleteList;
				const activeItem = autocompleteList.querySelector('.active');

				if (activeItem) {
					scrollToY(activeItem.offsetTop - 150, 500, autocompleteList);
				}
			});
		}
	}
};
</script>
