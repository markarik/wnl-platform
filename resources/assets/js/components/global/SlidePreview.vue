<template>
	<div ref="preview-modal" class="modal" :class="{'is-active': showModal}">
		<div class="modal-background" @click="$emit('closeModal')"></div>
		<div class="modal-card">
			<header class="modal-card-header">
				<slot name="header"></slot>
			</header>
			<div class="modal-card-body">
				<iframe name="slidePreview" :srcdoc="content" @load="onLoad" v-show="!isLoading"/>
			</div>
			<footer class="modal-card-footer">
				<slot name="footer"></slot>
			</footer>
		</div>
		<div class="next-slide" @click="$emit('nextSlide')">
			następny slajd
		</div>
		<button class="modal-close is-large" aria-label="close" @click="$emit('closeModal')"></button>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	.modal
		z-index: $z-index-alerts

	.next-slide
		width: 100px
		height: 100px
		background-color: blue
		z-index: $z-index-alerts+1

	.modal-card
		width: 90vw
		height: 90vh
		text-align: center
		background: white

		.modal-card-body
			height: 100%

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
				frames["slidePreview"].document.body.classList.add("is-without-controls")
				nextTick(() => this.isLoading = false)
			},
			onKeydown(e) {
				if (e.keyCode === 27) {
					this.$emit('closeModal')
				}
			}
		},
		watch: {
			'showModal' (newValue) {
				this.isLoading = newValue
			}
		},
		mounted() {
			document.body.addEventListener('keydown', this.onKeydown)
		},
		beforeDestroy() {
			document.body.removeEventListener('keydown', this.onKeydown)
		}
	}
</script>
