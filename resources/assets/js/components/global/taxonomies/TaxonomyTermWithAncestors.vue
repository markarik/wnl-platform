<template>
	<div @click="$emit('click', $event)" class="taxonomy-term" :class="{'is-bordered': isBordered}">
		<div class="taxonomy-term__content">
			<div class="taxonomy-term__content__parent">{{ancestors.map(ancestor => ancestor.tag.name).join(' > ')}}</div>
			<strong>{{term.tag.name}}</strong>
		</div>
		<slot></slot>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.taxonomy-term
		align-items: center
		display: flex
		line-height: $line-height-minus

		&__content
			&__parent
				color: $color-lighter-gray
				font-size: $font-size-minus-2

		&.is-bordered
			border: 1px solid $color-light-gray
			border-radius: $border-radius-small
			display: inline-flex
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
