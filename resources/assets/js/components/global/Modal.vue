<template>
	<div class="wnl-modal">
		<div class="modal" :class="{ 'is-active': isModalVisible }">
			<div class="modal-background" @click="$emit('closeModal')"></div>
			<div class="modal-content">
				<div class="box">
					<slot></slot>
				</div>
			</div>
			<button class="modal-close is-large" aria-label="close" @click="$emit('closeModal')"></button>
		</div>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-modal
		line-height: $line-height-base
		font-size: $font-size-base

		.modal
			z-index: $z-index-alerts

</style>

<script>
export default {
	name: 'Modal',
	props: {
		isModalVisible: {
			type: Boolean,
			default: false
		}
	},
}
</script>
