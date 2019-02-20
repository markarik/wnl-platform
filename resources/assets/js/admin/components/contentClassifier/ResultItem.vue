<template>
	<li class="result-item" :class="{'is-active': isActive}" @click="$emit('click', $event)">
		<div class="result-item__top-left"><slot name="top-left"></slot></div>
		<div class="result-item__content"><slot></slot></div>
		<span class="icon result-item__icon">
			<i class="fa fa-check-circle"></i>
		</span>
	</li>
</template>
<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.result-item
		border: $border-light-gray
		cursor: pointer
		display: flex
		flex-direction: column
		font-size: $font-size-minus-1
		line-height: $line-height-minus
		margin: $margin-tiny
		max-height: 200px
		min-height: 90px
		overflow: auto
		padding: $margin-base
		position: relative
		transition: border-width .3s ease-in-out, border-color .3s ease-in-out
		width: 160 + 4 * $margin-small

		&__top-left
			color: $color-lighter-gray
			font-size: $font-size-minus-3

		&__content
			text-align: center

		&__icon
			animation: fadein .3s
			color: $color-correct-shadow
			display: none
			position: absolute
			right: 5px
			top: 5px

			.is-active &
				display: block

		&.is-active
			border: 2px solid $color-correct-shadow
			border-radius: $border-radius-small

		@keyframes fadein
			from
				opacity: 0
			to
				opacity: 1
</style>

<script>
export default {
	props: {
		isActive: {
			type: Boolean,
			default: false,
		},
	}
};
</script>
