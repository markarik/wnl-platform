<template>
	<div class="switch" :class="classObject">
		<input type="checkbox" :name="name" :disabled="disabled" :value="value" @input="$emit('toggle')">
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

	.switch
		--height: 22px
		input
			opacity: 0
			display: inline-block
			width: 100%
			height: 100%
			position: absolute
			z-index: 1
			cursor: pointer

		appearance: none
		position: relative
		outline: 0
		border-radius: calc(1 * var(--height))
		width: calc(1.625 * var(--height))
		height: var(--height)
		background-color: $color-white
		/*border: 1px solid $color-background-gray*/
		cursor: pointer
		box-sizing: border-box
		display: inline-block
		vertical-align: middle
		-webkit-tap-highlight-color: transparent
		&:before,
		&:after
			content: " "
			position: absolute
			border-radius: calc((var(--height) - 2px) / 2)
			transition: .233s

		&:before
			background-color: $color-background-gray
			height: calc(var(--height) - 2px)
			left: 0
			top: 0
			width: calc(1.625 * var(--height) - 2px)

		&:after
			background-color: #FFF
			box-shadow: 0 2px 3px rgba(17, 17, 17, 0.1)
			left: 2px
			height: calc(var(--height) - 4px)
			top: 2px
			width: calc(var(--height) - 4px)

		&.checked
			border-color: $color-ocean-blue
			background-color: $color-ocean-blue
			&:before
				transform: scale(0)
			&:after
				transform: translateX(calc(0.625 * var(--height)))

		// Sizes
		&.is-small
			--height: 12px

		&.is-medium
			--height: 28px

		&.is-large
			--height: 32px
</style>

<script>
export default {
	name: 'wnl-toggler',
	props: [
		'disabled',
		'type',
		'size',
		'name',
		'value',
	],
	computed: {
		classObject () {
			const {type, size} = this;
			return {
				[`is-${type}`]: type,
				[`is-${size}`]: size,
				checked: !!this.value
			};
		}
	},
};
</script>
