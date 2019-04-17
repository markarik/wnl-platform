<template>
	<div v-if="isLoading" class="wnl-overlay">
		<span class="loader"></span>
		<span class="loader-text">{{text}}</span>
	</div>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'

	.wnl-overlay
		align-items: center
		background: rgba(255, 255, 255, 0.9)
		bottom: 0
		display: flex
		flex-direction: column
		justify-content: center
		left: 0
		position: fixed
		right: 0
		top: 0
		z-index: $z-index-overlay

		.loader
			height: 40px
			width: 40px

		.loader-text
			color: $color-ocean-blue
			margin-top: $margin-small
</style>

<script>
export default {
	name: 'TextOverlay',
	props: {
		isLoading: {
			type: Boolean,
			default: false
		},
		text: {
			type: String,
			default: 'Uwaga, nadjeżdża wiedza...',
		}
	}
};
</script>
