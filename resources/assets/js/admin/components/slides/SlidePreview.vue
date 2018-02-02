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
	.modal-content
		width: 90vw
		height: 90vh

		iframe
			width: 100%
			height: 100%
</style>

<script>
	export default {
		name: 'SlidePreview',
		props: {
			content: {
				type: String,
				default: ''
			},
			showModal: {
				type: Boolean,
				default: false
			}
		}
	}
</script>
