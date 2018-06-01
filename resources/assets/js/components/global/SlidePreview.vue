<template>
	<div
		ref="preview-modal"
		class="modal"
		:class="{'is-active': showModal}">
		<div
			class="previous-slide"
			v-show="hasManySlides">
			<span class="icon" @click="$emit('switchSlide', -1)">
				<i class="fa fa-angle-left"></i>
			</span>
		</div>
		<div class="modal-background" @click="$emit('closeModal')"></div>
		<div class="modal-card">
			<header class="modal-card-header">
				<slot name="header"></slot>
			</header>
			<div class="modal-card-body">
				<iframe
					name="slidePreview"
					:srcdoc="content"
					@load="onLoad"
					v-show="!isLoading"/>
			</div>
			<footer class="modal-card-footer">
				<slot name="footer"></slot>
			</footer>
		</div>
		<div
			class="next-slide"
			v-if="hasManySlides">
			<span class="icon" @click="$emit('switchSlide', 1)">
				<i class="fa fa-angle-right"></i>
			</span>
		</div>
		<button
			class="modal-close is-large"
			aria-label="close"
			@click="$emit('closeModal')"
		></button>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	.modal
		z-index: $z-index-alerts

	.next-slide, .previous-slide
		z-index: $z-index-alerts + 1
		text-align: center
		width: 10vw
		.icon
			text-align: center
			color: #929AA8
			display: inline-block
			vertical-align: middle
			width: 6em
			height: 6em
			.fa
				font-size: $font-size-plus-8

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
			},
			slidesCount: {
				type: Number,
			}
		},
		computed: {
			hasManySlides() {
				return this.slidesCount > 1
			}
		},
		methods: {
			onLoad() {
				frames["slidePreview"].document.body.classList.add("is-without-controls")
				nextTick(() => this.isLoading = false)
			},
			onKeydown(e) {
				switch(e.keyCode) {
					case 37: // left arrow
						e.stopPropagation()
						this.$emit('switchSlide', -1)
						break
					case 39: // right arrow
						e.stopPropagation()
						this.$emit('switchSlide', 1)
						break
					case 27: // esc
						this.$emit('closeModal')
						break
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
