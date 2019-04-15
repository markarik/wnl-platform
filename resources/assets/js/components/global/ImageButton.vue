<template>
	<div class="wnl-image-button-container" :class="{ 'is-reverse': isReverse, 'loading': loading }">
		<button class="button wnl-image-button without-image"
			:class="[iconClass, iconSizeClass]"
			:name="name"
			:disabled="disabled"
			:title="title"
			@click="emitClick">
			<wnl-icon :name="icon"></wnl-icon>
		</button>
		<label :for="name" class="wnl-image-button-label" v-if="label">
			{{label}}
		</label>
	</div>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

	@keyframes spin
		to
			transform: rotate(1turn)

	.is-reverse
		flex-direction: row-reverse

	.wnl-image-button-container
		display: flex
		align-items: center
		overflow: hidden
		transition-duration: 0.8s
		transition-property: transform

		&.loading .wnl-image-button svg
			animation: spin 1s infinite

	.wnl-image-button
		+small-shadow()

		border: 0
		padding: 0

		&.is-primary
			fill: $color-white

		&.without-image
			padding: 0

	.wnl-image-button-label
		font-size: $font-size-minus-1
		margin: 0 10px

	+rounded-square-standard-sizes('image-button')
</style>

<script>
export default {
	props: ['name', 'icon', 'alt', 'modifier', 'size', 'align', 'label', 'disabled', 'title', 'loading'],
	computed: {
		iconAlt () {
			return this.alt || this.icon;
		},
		iconClass () {
			let modifier = this.modifier || 'primary';
			return `is-${modifier}`;
		},
		iconSizeClass () {
			let size = this.size || 'medium';
			return `wnl-image-button-${size}`;
		},
		isReverse () {
			return this.align === 'right';
		}
	},
	methods: {
		emitClick() {
			this.$emit('buttonclicked');
		}
	}
};
</script>
