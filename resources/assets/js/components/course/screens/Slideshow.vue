<template>
	<div>
		<div class="wnl-slideshow-container">
			<div class="wnl-slideshow-background-control level">
				<div class="level-left">
				</div>
				<div class="level-right">
					<div class="level-item">Tło prezentacji:</div>
					<div class="level-item"><a class="white" @click="changeBackground('white')"></a></div>
					<div class="level-item"><a class="dark" @click="changeBackground('dark')"></a></div>
					<div class="level-item"><a class="image" @click="changeBackground('image')"></a></div>
				</div>
			</div>
			<div class="wnl-screen wnl-ratio-16-9">
				<div class="wnl-slideshow-content" :class="{ 'is-focused': isFocused, 'is-faux-fullscreen': isFauxFullscreen }">
					<div class="faux-fullscreen-close" v-if="isFauxFullscreen" @click="closeFauxFullscreen">
						<span class="icon is-medium"><i class="fa fa-times"></i></span>
					</div>
				</div>
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
		<wnl-qna></wnl-qna>
	</div>
</template>
<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

	.wnl-ratio-16-9
		padding-bottom: 56.25%
		position: relative
		width: 100%

	.wnl-slideshow-content
		border-left: $border-light-gray
		border-top: $border-light-gray
		border-right: $border-light-gray
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

	.wnl-slideshow-background-control
		border-top: $border-light-gray
		font-size: $font-size-minus-1
		line-height: $line-height-plus
		margin: $margin-base 0 $margin-small 0
		padding-top: $margin-base
		text-align: right
		text-transform: uppercase
		vertical-align: middle

		a
			border-radius: $border-radius-full
			display: inline-block
			height: 2em
			margin-left: $margin-small
			width: 2em

			&.white
				border: 1px solid $color-inactive-gray

			&.dark
				background: $color-gray

			&.image
				+gradient-horizontal($gradient-bg-image-left, $gradient-bg-image-right)
</style>

<script>
	import _ from 'lodash'
	import Postmate from 'postmate'
	import screenfull from 'screenfull'

	import SlideshowNavigation from './SlideshowNavigation.vue'
	import Qna from 'js/components/qna/Qna.vue'
	import { isDebug, getUrl } from 'js/utils/env'

	export default {
		name: 'Slideshow',
		components: {
			'wnl-slideshow-navigation': SlideshowNavigation,
			'wnl-qna': Qna,
		},
		data() {
			return {
				child: {},
				currentSlide: 1,
				loaded: false,
				isFauxFullscreen: false,
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
				return this.$route.params.screenId
			},
			slideshowId() {
				return this.screenData.meta.resources[0].id
			},
			slideshowUrl() {
				return getUrl(`slideshow-builder/${this.slideshowId}`)
			},
			slideshowElement() {
				return this.container.getElementsByTagName('iframe')[0]
			},
			slideshowSizeClass() {
				return this.isFauxFullscreen ? 'is-faux-fullscreen' : 'wnl-ratio-16-9'
			},
			iframe() {
				if (this.loaded) {
					return this.$el.getElementsByTagName('iframe')[0]
				}
			},
		},
		methods: {
			closeFauxFullscreen() {
				this.isFauxFullscreen = false
				this.focusSlideshow()
			},
			toggleFullscreen() {
				if (screenfull.enabled) {
					screenfull.toggle(this.slideshowElement)
				} else {
					this.isFauxFullscreen = true
				}
				this.focusSlideshow()
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
			changeBackground(background = 'image') {
				this.child.call('changeBackground', background)
				this.focusSlideshow()
			},
			focusSlideshow() {
				this.iframe.click()
				this.iframe.focus()
				this.isFocused = true
			},
			checkFocus() {
				this.isFocused = this.iframe === document.activeElement
			},
			initSlideshow() {
				$wnl.logger.debug('Initiating slideshow')
				new Postmate({
						container: this.container,
						url: this.slideshowUrl
					}).then(child => {
						this.child = child
						this.loaded = true
						this.setEventListeners()

						this.goToSlide(this.slideNumber)
						this.focusSlideshow()
					}).catch(exception => $wnl.logger.capture(exception))
			},
			messageEventListener(event) {
				if (typeof event.data === 'string') {
					try {
						let data = JSON.parse(event.data)
						if (data.namespace === 'reveal' &&
							data.eventName === 'slidechanged' &&
							this.slideChanged === false)
						{
							this.focusSlideshow()
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
				addEventListener('message', _.debounce(
					this.messageEventListener.bind(this),
					100, {
						leading: true,
						trailing: true,
					})
				),
				addEventListener('blur', this.checkFocus)
				addEventListener('focus', this.checkFocus)
				addEventListener('focusout', this.checkFocus)
			},
			destroySlideshow() {
				if (typeof this.child.destroy === 'function') {
					this.child.destroy()
				}
				removeEventListener('blur', this.checkFocus)
				removeEventListener('focus', this.checkFocus)
				removeEventListener('focusout', this.checkFocus)
				removeEventListener('message', this.messageEventListener)
				this.loaded = false
			},
		},
		mounted() {
			Postmate.debug = false
			this.initSlideshow()
		},
		watch: {
			'$route' (to, from) {
				if (to.params.screenId !== from.params.screenId) {
					this.destroySlideshow()
				}
				if (this.loaded && this.slide !== this.currentSlide) {
					this.goToSlide(this.slideNumber)
				}
			},
			'screenData' (newValue, oldValue) {
				if (newValue.type === 'slideshow') {
					this.initSlideshow()
				}
			},
		}
	}
</script>
