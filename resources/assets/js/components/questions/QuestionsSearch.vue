<template lang="html">
	<div class="wnl-questions-search">
		<div class="search-input">
					<div class="control" :class="{'is-loading': loading}">
						<input
							class="input"
							placeholder="Szukaj pytań..."
							ref="input"
							type="text"
							@input="debounceInput"
						>
					</div>
				</div>
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
			width: 100%

			&:focus
				outline: none
</style>

<script>
	export default {
		data() {
			return {
				loading: false
			}
		},
		methods: {
			debounceInput: _.debounce(function({target: {value}}) {
				this.$emit('search', value)
			}, 500),
		}
	}
</script>