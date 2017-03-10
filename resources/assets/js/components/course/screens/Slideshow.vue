<template>
	<div class="wnl-slideshow-container">
		<div class="wnl-snippet wnl-ratio-16-9">
			<div class="wnl-slideshow-content" id="wnl-slideshow"></div>
		</div>
		<div class="wnl-slideshow-controls">
			<div class="wnl-slideshow-controls-left">
				<wnl-slideshow-navigation></wnl-slideshow-navigation>
			</div>
			<div class="wnl-slideshow-controls-right">
				<wnl-image-button name="wnl-slideshow-control-fullscreen"
					icon="fullscreen-arrows"
					alt="Włącz pełen ekran"
					align="right"
					label="Pełen ekran"
					@buttonclicked="toggleFullscreen"
				></wnl-image-button>
			</div>
		</div>
	</div>
</template>
<style lang="sass">
	.wnl-ratio-16-9
		padding-bottom: 56.25%
		position: relative
		width: 100%

	.wnl-slideshow-content
		bottom: 0
		left: 0
		position: absolute
		right: 0
		top: 0

		iframe
			height: 100%
			width: 100%

	.wnl-slideshow-controls
		display: flex
		justify-content: space-between
		margin-top: 10px
</style>
<script>
	import screenfull from 'screenfull'
	import Postmate from 'postmate'
	import SlideshowNavigation from './SlideshowNavigation.vue'
	import { isDebug, getUrl } from 'js/utils/env'

	export default {
		name: 'Slideshow',
		components: {
			'wnl-slideshow-navigation': SlideshowNavigation
		},
		data() {
			return {
				child: {},
				currentSlide: 1,
				loaded: false
			}
		},
		props: ['screenData', 'slide'],
		computed: {
			slideNumber() {
				return Math.max(this.slide - 1, 0) || 0
			},
			container() {
				return document.getElementById('wnl-slideshow')
			},
			snippetId() {
				return this.screenData.id
			},
			slideshowUrl() {
				return getUrl(`slideshow-builder/${this.snippetId}`)
			},
			slideshowElement() {
				return document.getElementById('wnl-slideshow')
			}
		},
		methods: {
			setCurrentSlideFromIndex(slideIndex) {
				this.currentSlide = slideIndex + 1
			},
			goToSlide(slideNumber) {
				this.currentSlide = slideNumber
				this.child.call('goToSlide', slideNumber)
			},
			setEventListeners() {
				window.addEventListener('message', event => {
					if (typeof event.data === 'string') {
						let data = JSON.parse(event.data)
						if (data.namespace === 'reveal' && data.eventName === 'slidechanged') {
							this.setCurrentSlideFromIndex(data.state.indexh)
							this.$router.replace({
								name: 'screens',
								params: { slide: this.currentSlide },
								query: { sc: '1' }
							})
						}
					}
				})
			},
			toggleFullscreen() {
				if (screenfull.enabled) {
					screenfull.toggle(this.slideshowElement)
				}
			}
		},
		mounted() {
			Postmate.debug = isDebug()

			const handshake = new Postmate({
				container: this.container,
				url: this.slideshowUrl
			})
			handshake.then(child => {
				child.on('loaded', (status) => {
					if (status) {
						this.child = child
						this.goToSlide(this.slideNumber)
						this.setEventListeners()
					}
				})
			})
		},
		watch: {
			'$route' (to, from) {
				if (!to.query.hasOwnProperty('sc') && this.slideNumber !== this.currentSlide) {
					this.goToSlide(this.slideNumber)
				}
			}
		}
	}
</script>
