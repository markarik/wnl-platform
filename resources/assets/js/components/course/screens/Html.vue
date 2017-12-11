<template>
	<div class="wnl-screen-html" :class="{'wnl-repetitions': isRepetitions}" @click="onClick">
		<div class="content" v-html="content">
		</div>
		<p class="end-button has-text-centered" v-if="showBacklink">
			<router-link :to="{name: 'dashboard'}" class="button is-primary is-outlined">
				Wróć do auli
			</router-link>
		</p>
		<div class="image-gallery-wrapper" :class="{ isVisible: isVisible }">
			<div class="image-container"></div>
			<span class="prev" @click="goToImage(previousImageIndex)"><i class="fa fa-angle-left"></i></span>
			<span class="next" @click="goToImage(nextImageIndex)"><i class="fa fa-angle-right"></i></span>
			<span class="iv-close" @click="isVisible = false"></span>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-screen-html
		margin: $margin-big 0

		.image-gallery-wrapper
			display: none
			position: absolute
			z-index: 9999
			&.isVisible
				display: block
			.image-container
				display: block
				width: 100%
				height: 100%
				position: fixed
				background-color: blue
				top: 0
				left: 0
			.prev,.next
				position: absolute
				color: red
				height: 100%
				width: 25%
				display: inline-block
				top: 0
				z-index: 1
			.next
				right: 0
				text-align: right

		img:hover
			opacity: 0.7

		.content
			font-size: $font-size-plus-1
			line-height: $line-height-plus


	.wnl-repetitions
		ol
			counter-reset: li
			margin-left: 0
			padding-left: $margin-base

		ol li
			list-style-type: none

			&::before
				color: $color-ocean-blue
				content: counter(li) ". "
				counter-increment: li
				font-weight: $font-weight-bold

		ol li:nth-child(5n)
			margin-bottom: $margin-big

		ol li:nth-child(10n-4),
		ol li:nth-child(10n-3),
		ol li:nth-child(10n-2),
		ol li:nth-child(10n-1),
		ol li:nth-child(10n)

			&::before
				color: $color-purple
</style>

<script>
	import _ from 'lodash'
	import {imageviewer} from 'vendor/imageviewer/imageviewer'

	imageviewer($, window, document)
	function showImage(src) {
		// this is not Vue component, it's "event triggered" this
		ImageViewer($('.image-gallery-wrapper .image-container'), {snapViewPersist: false}).load(src);
	}


	export default {
		name: 'Html',
		props: ['screenData', 'showBacklink'],
		data() {
			return {
				imagesLoaded: false,
				imagesSelector: '.wnl-screen-html img',
				isVisible: false,
				images: [],
				currentImageIndex: -1,
			}
		},
		computed: {
			content() {
				return this.screenData.content
			},
			isRepetitions() {
				return this.screenData.name.indexOf('Powtórki') > -1
			},
			previousImageIndex() {
				return this.currentImageIndex > 0 ? this.currentImageIndex - 1 : this.images.length -1
			},
			nextImageIndex() {
				return this.currentImageIndex === this.images.length -1 ? 0 : this.currentImageIndex + 1
			},
		},
		methods: {
			goToImage(index) {
				if (index < 0) return
				showImage(this.images[index].src)
				this.currentImageIndex = index
			},
			wrapEmbedded() {
				let iframes = this.$el.getElementsByClassName('ql-video'),
					wrapperClass = 'ratio-16-9-wrapper'

				if (iframes.length > 0) {
					_.each(iframes, (iframe) => {
						let wrapper = document.createElement('div'),
							parent = iframe.parentNode

						wrapper.className = wrapperClass
						parent.replaceChild(wrapper, iframe)
						wrapper.appendChild(iframe)
					})
				}
			},
			addFullscreen() {
				this.images = document.querySelectorAll(this.imagesSelector)

				if (this.images.length) {
					this.imagesLoaded = true
				}

				this.images.forEach((image, index) => {
					image.addEventListener('click', (event) => {
						this.isVisible = true
						this.goToImage(index)
					});
				})
			},
			onClick(event) {
				if (!this.imagesLoaded) {
					this.images = document.querySelectorAll(this.imagesSelector)
					this.images.forEach((image, index) => {
						image.addEventListener('click', (event) => {
							this.isVisible = true
							this.goToImage(index)
						});
					})

					if (event.target.matches(this.imagesSelector)) {
						const index = this.images.findIndex((image) => image.src === event.target.src)
						this.goToImage(index)
						this.imagesLoaded = true
						this.isVisible = true
					}
				}
			}
		},
		beforeDestroy() {
			document.removeEventListener('click', showImage);
		},
		mounted() {
			this.wrapEmbedded();
			this.addFullscreen();
			$('body').on('click', '#iv-container .iv-close', () => {
				this.isVisible = false
			})
		},
		updated() {
			this.wrapEmbedded()
		},
	}
</script>
