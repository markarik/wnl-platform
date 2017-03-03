<template>
	<div class="wnl-snippet wnl-ratio-16-9">
		<div class="wnl-slideshow-container" id="wnl-slideshow"></div>
	</div>
</template>
<style lang="sass">
	.wnl-ratio-16-9
		padding-bottom: 56.25%
		position: relative
		width: 100%

	.wnl-slideshow-container
		bottom: 0
		left: 0
		position: absolute
		right: 0
		top: 0

		iframe
			height: 100%
			width: 100%
</style>
<script>
	import Postmate from 'postmate'

	export default {
		name: 'Slideshow',
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
				return $fn.getUrl('slideshow-builder/' + this.snippetId)
			},
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
							this.$router.replace({ name: 'screens', params: { slide: this.currentSlide } })
						}
					}
				})
			}
		},
		mounted() {
			Postmate.debug = global.$fn.isDevEnv()

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
				if (this.slideNumber !== this.currentSlide) {
					this.goToSlide(this.slideNumber)
				}
			}
		}
	}
</script>
