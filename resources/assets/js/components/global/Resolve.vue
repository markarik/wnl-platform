<template>
	<p
		v-if="resource.resolved"
		class="resolvable icon"
		@click="$emit('unresolveResource')"
	>
		<i class="fa fa-undo" />
		<span v-t="'ui.action.undo'" />
	</p>
	<span v-else class="resolvable icon">
		<i class="fa fa-check" @click="$emit('resolveResource')" />
	</span>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.resolvable
		cursor: pointer
		width: auto

		&:hover
			color: $color-red

		span
			margin-left: $margin-small

		i
			font-size: $font-size-minus-1
			margin-left: $margin-small

</style>

<script>
export default {
	props: {
		resource: {
			type: Object,
			required: true
		}
	},
};
</script>
