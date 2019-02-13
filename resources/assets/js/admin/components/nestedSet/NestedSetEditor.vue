<template>
	<div class="nested-set-editor">
		<div class="nested-set-editor__panel is-left">
			<div class="nested-set-editor__panel__header">
				<slot name="header"></slot>
			</div>
			<slot name="nodesList" v-if="!isLoading"/>
			<wnl-text-loader v-else />
		</div>
		<div class="nested-set-editor__panel is-right">
			<slot name="panelRight"></slot>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.nested-set-editor
		border-top: 1px solid $color-lightest-gray
		display: flex

		&__panel
			flex: 50%

			&.is-left
				border-right: 1px solid $color-lightest-gray
				padding-right: $margin-big

			&.is-right
				padding-left: $margin-big

			&__header
				align-items: center
				background-color: $color-white
				display: flex
				justify-content: space-between
				padding: $margin-big 0 $margin-base
				position: sticky
				top: -30px
				z-index: 1

				&__search
					flex-grow: 1
					margin-left: $margin-big
</style>

<script>
export default {
	props: {
		isLoading: {
			default: false,
			type: Boolean
		},
	}
};
</script>
