<template>
	<div
		class="taxonomy-term"
		:class="{'is-bordered': isBordered}"
		:style="style"
		@click="$emit('click', $event)"
	>
		<slot name="left" />
		<div class="taxonomy-term__content" :class="{'has-parent': ancestors.length}">
			<div class="taxonomy-term__content__parent">{{ancestors.map(ancestor => ancestor.tag.name).join(' > ')}}</div>
			<strong>{{term.tag.name}}</strong>
		</div>
		<slot name="right" />
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.taxonomy-term
		align-items: center
		display: flex
		line-height: $line-height-minus

		.has-parent
			font-size: $font-size-minus-1

		&__content
			&__parent
				color: $color-lighter-gray
				font-size: $font-size-minus-2

		&.is-bordered
			border: 1px solid $color-light-gray
			border-radius: $border-radius-small
			display: inline-flex
			margin: $margin-small-minus $margin-medium $margin-small-minus 0
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
	computed: {
		style() {
			const color = this.term.taxonomy && this.term.taxonomy.color;
			if (!color || !this.isBordered) return {};

			return {
				borderColor: color
			};
		}
	}
};
</script>
