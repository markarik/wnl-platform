<template>
	<div class="wnl-modal">
		<div class="modal is-active">
			<div class="modal-background" @click.stop="$emit('closeModal')" />
			<div class="modal-content">
				<div class="box">
					<slot />
				</div>
			</div>
			<button
				class="modal-close is-large"
				aria-label="close"
				@click.stop="$emit('closeModal')"
			/>
		</div>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-modal
		line-height: $line-height-base
		font-size: $font-size-base

		.modal
			z-index: $z-index-alerts +1

</style>

<script>
export default {
	name: 'Modal',
};
</script>
