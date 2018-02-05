<template>
	<div ref="preview-modal" class="modal" :class="{'is-active': showModal}">
		<div class="modal-background" @click="$emit('closeModal')"></div>
		<div class="modal-card">
			<header class="modal-card-header">
				<slot name="header"></slot>
			</header>
				<iframe name="slidePreview" :srcdoc="content" @load="onLoad()" v-show="!isLoading"/>
			<footer class="modal-card-footer">
				<slot name="footer"></slot>
			</footer>
		</div>
		<button class="modal-close is-large" aria-label="close" @click="$emit('closeModal')"></button>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	.modal
		z-index: $z-index-alerts

	.modal-card
		width: 90vw
		height: 90vh
		text-align: center
		background: white

		iframe
			width: 100%
			height: 100%

		footer
			height: 5%
</style>

<script>

	import {nextTick} from 'vue'

	export default {
		name: 'SlidePreview',
		data() {
			return {
				isLoading: true
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
			onLoad() {
				this.addClass().then(() => {
					this.isLoading = false
				})
			},
			addClass() {
				return new Promise((resolve) => {
					frames["slidePreview"].document.body.classList.add("is-without-controls")
					nextTick(resolve)
				})
			}
		},
		watch: {
			'showModal' (newValue, oldValue) {
				if (newValue) this.isLoading = true
			}
		}
	}
</script>
