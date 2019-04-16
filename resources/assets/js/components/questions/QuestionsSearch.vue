<template>
	<div class="wnl-questions-search">
		<div class="search-input">
			<div class="control" :class="{'is-loading': loading}">
				<input
					ref="input"
					v-model="searchPhrase"
					class="input is-hovered"
					:placeholder="$t('questions.filters.searchPlaceholder')"
					type="text"
					:disabled="loading"
					@keydown.enter="onEnter"
				>
			</div>
		</div>
		<a
			class="search-button button is-primary is-outlined is-small"
			@click="search"
		>
			{{$t('questions.filters.searchButton')}}
		</a>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-questions-search
		padding-left: $margin-base
		padding-right: $margin-base
		padding-top: $margin-base

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
		height: $search-input-height


</style>

<script>
export default {
	name: 'QuestionsSearch',
	props: ['loading'],
	data() {
		return {
			searchPhrase: '',
		};
	},
	methods: {
		search() {
			this.$emit('emitValueToFilter', this.searchPhrase);
		},
		onEnter() {
			this.$emit('emitValueToFilter', this.searchPhrase);
		}
	}
};
</script>
