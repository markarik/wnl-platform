<template>
	<div @click="$emit('click', $event)">
		<div class="parent-term">{{ancestors.map(ancestor => ancestor.tag.name).join(' > ')}}</div>
		<strong>{{term.tag.name}}</strong>
		<slot></slot>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.parent-term
		color: $color-lighter-gray
		font-size: $font-size-minus-2

</style>

<script>
export default {
	props: {
		term: {
			type: Object,
			required: true,
		},
		ancestors: {
			type: Array,
			default: () => [],
		},
	},
};
</script>
