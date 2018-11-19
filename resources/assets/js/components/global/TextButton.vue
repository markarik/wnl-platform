<template lang="html">
	<button class="wnl-text-button"
			:disabled="disabled"
			:type="type"
			@click="emitClick"
	>
		<i v-if="icon" :class="`icon fa fa-${icon}`"></i>
		<slot />
	</button>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'

	.wnl-text-button
		align-items: center
		background: none
		border: 0
		color: $color-ocean-blue
		cursor: pointer
		display: inline-flex
		font-weight: bold
		outline: none
		padding: 0

	.icon
		height: auto
</style>

<script>
	export default {
		props: ['icon', 'disabled', 'type'],
		methods: {
			emitClick() {
				this.$emit('click')
			}
		}
	}
</script>
