<template>
	<div class="wnl-slideshow-container">
		<div class="wnl-screen wnl-ratio-16-9">
			<div class="wnl-slideshow-content" :class="{ 'is-focused': isFocused }"></div>
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
	@import 'resources/assets/sass/variables'

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
			opacity: 0.25
			transition: opacity $transition-length-base
			width: 100%

		&.is-focused

			iframe
				opacity: 1
				transition: opacity $transition-length-base

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
				loaded: false,
				isFocused: false,
				slideChanged: false
			}
		},
		props: ['screenData', 'slide'],
		computed: {
			slideNumber() {
				return Math.max(this.slide - 1, 0) || 0
			},
			container() {
				return this.$el.getElementsByClassName('wnl-slideshow-content')[0]
			},
			screenId() {
				return this.screenData.id
			},
			slideshowUrl() {
				return getUrl(`slideshow-builder/${this.screenId}`)
			},
			slideshowElement() {
				return this.container.getElementsByTagName('iframe')[0]
			},
			iframe() {
				if (this.loaded) {
					return this.$el.getElementsByTagName('iframe')[0]
				}
			},
		},
		methods: {
			toggleFullscreen() {
				if (screenfull.enabled) {
					screenfull.toggle(this.slideshowElement)
					this.focusSlideshow()
				}
			},
			slideNumberFromIndex(index) {
				return index + 1
			},
			setCurrentSlideFromIndex(slideIndex) {
				this.currentSlide = this.slideNumberFromIndex(slideIndex)
			},
			goToSlide(slideNumber) {
				this.slideChanged = true

				this.currentSlide = this.slideNumberFromIndex(slideNumber)
				this.child.call('goToSlide', slideNumber)

				this.focusSlideshow()
			},
			focusSlideshow() {
				this.iframe.focus()
				this.isFocused = true
			},
			checkFocus() {
				this.isFocused = this.iframe === document.activeElement
			},
			initSlideshow() {
				new Postmate({
						container: this.container,
						url: this.slideshowUrl
					}).then(child => {
						this.child = child
						this.loaded = true
						this.setEventListeners()

						this.goToSlide(this.slideNumber)
						this.focusSlideshow()
					})
			},
			messageEventListener(event) {
				if (typeof event.data === 'string') {
					try {
						let data = JSON.parse(event.data)
						if (data.namespace === 'reveal' &&
							data.eventName === 'slidechanged' &&
							this.slideChanged === false)
						{
							this.setCurrentSlideFromIndex(data.state.indexh)
							this.$router.replace({
								name: 'screens',
								params: { slide: this.currentSlide }
							})
						}

						this.slideChanged = false
					} catch (err) {}
				}
			},
			setEventListeners() {
				addEventListener('message', this.messageEventListener, event)
				addEventListener('blur', this.checkFocus)
				addEventListener('focus', this.checkFocus)
				addEventListener('focusout', this.checkFocus)
			},
			destroySlideshow() {
				this.child.destroy()
				removeEventListener('blur', this.checkFocus)
				removeEventListener('focus', this.checkFocus)
				removeEventListener('focusout', this.checkFocus)
				removeEventListener('message', this.messageEventListener)
			},
		},
		beforeDestroy() {
			this.destroySlideshow()
		},
		mounted() {
			Postmate.debug = isDebug()
			this.initSlideshow()
		},
		watch: {
			'$route' (to, from) {
				if (this.loaded && this.slide !== this.currentSlide) {
					console.log('routeChanged')
					this.goToSlide(this.slideNumber)
				}
			}
		}
	}
</script>
