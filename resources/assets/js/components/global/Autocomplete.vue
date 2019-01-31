<template>
	<div
		class="autocomplete-box"
		v-bind:class="{'is-down': isDown}"
		v-show="hasItems || $slots.footer"
	>
		<ul
			class="autocomplete-box__list"
			tabindex="-1"
			@keydown="onKeyDown"
		>
			<li
				class="autocomplete-box__item"
				v-for="item in items"
				@click="onItemChosen(item)"
				v-bind:class="{ active: item.active }"
				v-bind:key="item.id"
			>
				<slot v-bind:item="item"></slot>
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
				color: $autocomplete-active-item-text-color

		&__text
			padding: 5px 10px
</style>

<script>
import autocompleteNav from 'js/mixins/autocomplete-nav';

export default {
	name: 'Autocomplete',
	props: ['items', 'onItemChosen', 'isDown'],
	mixins: [autocompleteNav],
};
</script>
