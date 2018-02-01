<template>
	<div ref="preview-modal" class="modal" :class="{'is-active': showModal}">
		<div class="modal-background" @click="$emit('closeModal')"></div>
		<div class="modal-content">
			<iframe :srcdoc="content"/>
		</div>
		<button class="modal-close is-large" aria-label="close" @click="$emit('closeModal')"></button>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	.modal
		z-index: $z-index-alerts

	.modal-content
		width: 90vw
		height: 90vh

		frame
			width: 100%
			height: 100%
</style>

<script>
	export default {
		name: 'SlidePreview',
		data() {
			return {
				modal: false,
			}
		},
		props: {
			content: {
				type: String,
				default: ''
			},
			showModal: {
				type: Boolean,
				default: false
			}
		},
		methods: {
			closeModal() {
				this.modal = false
			}
		}
	}
</script>
