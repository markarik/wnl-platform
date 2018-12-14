<template>
	<div class="wnl-dropdown">
		<div class="activator" :class="{ 'is-active' : isActive }" @click="toggleActive">
			<slot name="activator"></slot>
			<div v-if="isActive" class="box drawer" :class="{'is-mobile': isMobile, 'is-wide': options.isWide}">
				<slot name="content"></slot>
			</div>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

	$header-height: 40px
	$footer-height: 40px
	$body-margin-top: $header-height
	$body-margin-bottom: $footer-height

	.activator
		align-items: center
		height: 100%
		justify-content: center
		position: relative
		cursor: pointer

		&.is-active:before
			content: ""
			position: absolute
			right: calc(50% - 7px)
			bottom: -1px
			width: 0
			height: 0
			border-style: solid
			border-width: 0 10px 10px 10px
			border-color: transparent transparent $color-white transparent
			z-index: $z-index-alerts -1

	.wnl-dropdown
		height: 100%
		min-height: 100%
		position: relative
		width: 100%

	.drawer
		border: $border-light-gray
		box-shadow: 2px 2px 10px 5px rgba(67,73,90,0.3)
		padding: 0
		position: absolute
		right: 0
		top: 100%
		z-index: 100

		&.is-wide
			max-width: 100vw
			width: 440px

			&.is-mobile
				border-radius: 0
				position: fixed
				top: $navbar-height

	.notifications-toggle
		align-items: center
		color: $color-gray-dimmed
		cursor: pointer
		display: flex
		height: 100%
		width: 100%
		justify-content: center
		min-height: 100%
		position: relative

		.loader
			border-bottom-color: $color-ocean-blue
			border-right-color: $color-ocean-blue
			border-left-color: $color-ocean-blue
			position: absolute
			z-index: 1
			bottom: ($navbar-height / 4.2)
			left: ($navbar-height / 2)

			.fas.fa-circle-notch
				color: $color-ocean-blue

		&.is-active
			background-color: $color-background-light-gray
			color: $color-gray

		&.is-off
			color: $color-inactive-gray

			&.is-active
				color: $color-white

		.icon
			margin: 0 $margin-tiny

	.counter
		align-items: center
		background: $color-ocean-blue
		border-radius: $border-radius-full
		color: $color-white
		display: flex
		font-size: $font-size-minus-3
		font-weight: $font-weight-black
		justify-content: center
		height: 1.7em
		position: absolute
		left: ($navbar-height / 2)
		top: $margin-medium
		width: 1.7em
		z-index: 1

	.feed
		position: relative

	.feed-header,
	.feed-footer
		align-items: center
		background: $color-white
		display: flex
		position: absolute
		width: 100%
		z-index: $z-index-overlay

	.feed-header
		border-radius: $border-radius-small $border-radius-small 0 0
		border-bottom: $border-light-gray
		height: $header-height
		justify-content: space-between
		padding: $margin-small $margin-medium
		top: 0

		.feed-heading
			font-size: $font-size-minus-2
			font-weight: $font-weight-bold
			text-transform: uppercase

	.feed-body
		height: 70vh
		max-height: 390px
		overflow-y: auto

		.feed-content
			padding: $body-margin-top 0 $body-margin-bottom

	.feed-footer
		+white-shadow-top()

		align-items: center
		bottom: 0
		border-radius: 0 0 $border-radius-small $border-radius-small
		border-top: $border-light-gray
		height: $footer-height
		justify-content: center
		padding: $margin-small $margin-medium

	.zero-state
		align-items: center
		display: flex
		flex-direction: column
		justify-content: center
		height: 100%
		padding: $margin-big
		width: 100%

		.zero-state-image
			min-width: 150px
			width: 50%

		.zero-state-text
			color: $color-gray-dimmed
			font-size: $font-size-minus-1
			margin-top: $margin-big
			text-align: center
</style>

<script>
import {mapGetters} from 'vuex';

export default {
	name: 'Dropdown',
	props: {
		options: {
			default() {
				return {
					isWide: false
				};
			},
			type: Object,
		}
	},
	data() {
		return {
			isActive: false,
		};
	},
	computed: {
		...mapGetters(['isMobile'])
	},
	methods: {
		clickHandler({target}) {
			if (this.isActive && !this.$el.contains(target)) {
				this.isActive = false;
				this.$emit('toggled', false);
			}
		},
		toggleActive() {
			this.isActive = !this.isActive;
			this.$emit('toggled', this.isActive);
		},
	},
	beforeDestroy() {
		document.removeEventListener('click', this.clickHandler);
	},
	mounted() {
		document.addEventListener('click', this.clickHandler);
	},
};
</script>
