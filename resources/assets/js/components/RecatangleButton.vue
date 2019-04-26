<template>
	<div
		class="rectangle-button"
		@click="$emit('click')"
	>
		<slot />
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/styleguide/settings/colors'
	@import 'resources/assets/sass/variables'

	.rectangle-button
		border: 1px solid $color-cadet-blue
		align-items: center
		cursor: pointer
		display: inline-flex
		font-size: $font-size-minus-1
		text-transform: uppercase
		position: relative
		transition: all 0.2s
		min-height: 28px
		line-height: 1em
		min-width: 50px
		background-color: $color-white
		justify-content: center
		padding: 0 $margin-small
</style>
