<template lang="html">
	<div class="wnl-questions-search">
		<div class="search-input">
			<div class="control" :class="{'is-loading': loading}">
				<input
					class="input is-hovered"
					placeholder="Wpisz frazę..."
					ref="input"
					type="text"
				>
			</div>
		</div>
		<a class="search-button button is-primary is-outlined is-small"
			@click="search"
			>
			{{$t('questions.filters.searchButton')}}
		</a>
	</div>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'

	.search-input
		align-items: center
		background: $color-white
		display: flex
		justify-content: space-between
		width: 100%

		.control
			flex: 1 auto
			margin-bottom: $margin-medium

		.input
			background: $color-white
			box-shadow: none
			width: 98%
			height: $search-input-height

			&:focus
				outline: none

	.search-button
		position: relative
		top: -$margin-small-minus


</style>

<script>
	export default {
		name: 'QuestionsSearch',
		data() {
			return {
				loading: false,
			}
		},
		methods: {
			search() {
				this.$emit('emitValueToFilter', this.$refs.input.value)
			}
		},
	}
</script>
