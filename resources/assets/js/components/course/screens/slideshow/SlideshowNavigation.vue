<template>
	<div class="nav-container">
		<label class="label" for="wnl-slideshow-navigation-">
			Idź do slajdu
		</label>
		<input
			class="input"
			type="number"
			size="3"
			maxlength="3"
			min="1"
			v-model.number="slideNumber"
			@input="onChange"
		>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.nav-container
		align-items: center
		display: flex

	.input
		border: 0
		border-bottom: 1px solid $color-ocean-blue
		border-radius: 0
		box-shadow: none
		color: $color-darkest-gray
		font-size: $font-size-minus-1
		line-height: 1em
		text-align: center
		width: 4em

	.label
		flex: 1 0 auto
		font-size: $font-size-minus-2
		font-weight: $font-weight-regular
		margin: 0 5px
</style>

<script>
import { debounce } from 'lodash';

export default {
	name: 'SlideshowNavigation',
	data() {
		return {
			slideNumber: 1
		};
	},
	methods: {
		onChange: debounce(function () {
			if (parseInt(this.slideNumber) > 0) {
				this.$emit('navigateToSlide', this.slideNumber);
			}
		}, 500),
	}
};
</script>
