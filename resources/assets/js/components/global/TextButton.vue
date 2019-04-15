<template>
	<button
		class="wnl-text-button"
		:disabled="disabled"
		:type="type"
		@click="$emit('click', $event)"
	>
		<i v-if="icon" :class="`icon fa fa-${icon}`"></i>
		<slot />
	</button>
</template>

<style lang="sass" scoped>
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
};
</script>
