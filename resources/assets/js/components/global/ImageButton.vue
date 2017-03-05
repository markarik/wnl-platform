<template lang="html">
	<button class="wnl-image-button" :class="[iconClass, iconSizeClass]" :name="name">
		<wnl-icon :name="icon"></wnl-icon>
	</button>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

	.wnl-image-button
		border: 0
		padding: 0

	.wnl-image-button-primary
		background-color: $color-ocean-blue
		fill: $color-white

	+rounded-square-standard-sizes('image-button')
</style>

<script>
	export default {
		props: ['name', 'icon', 'alt', 'modifier', 'size'],
		computed: {
			iconAlt () {
				return this.alt || this.icon
			},
			iconClass () {
				let modifier = this.modifier || 'primary'
				return 'wnl-image-button-' + modifier
			},
			iconSizeClass () {
				let size = this.size || 'medium'
				return 'wnl-image-button-' + size
			}
		}
	}
</script>
