<template>
	<div @click="$emit('click', $event)" class="taxonomy-term" :class="{'is-bordered': isBordered}">
		<div class="taxonomy-term__parent">{{ancestors.map(ancestor => ancestor.tag.name).join(' > ')}}</div>
		<strong>{{term.tag.name}}</strong>
		<slot></slot>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.taxonomy-term
		line-height: $line-height-minus

		&__parent
			color: $color-lighter-gray
			font-size: $font-size-minus-2

		&.is-bordered
			border: 1px solid #efefef
			border-radius: 4px
			display: inline-block
			margin-right: $margin-medium
			padding: $margin-small

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
		isBordered: {
			type: Boolean,
			default: false,
		}
	},
};
</script>
