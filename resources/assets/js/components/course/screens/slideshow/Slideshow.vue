<template>
	<div>
		<div class="wnl-slideshow-container">
			<div class="wnl-slideshow-background-control">
				<div class="controls-left">
					<wnl-slideshow-navigation></wnl-slideshow-navigation>
				</div>
				<div class="controls-right">
					<div class="controls-item">
						Tło
						<a class="white" @click="changeBackground('white')"></a>
						<a class="dark" @click="changeBackground('dark')"></a>
						<a class="image" @click="changeBackground('image')"></a>
					</div>
				</div>
			</div>
			<div class="wnl-screen wnl-ratio-16-9">
				<div class="wnl-slideshow-content" :class="{ 'is-focused': isFocused, 'is-faux-fullscreen': isFauxFullscreen }">
					<div class="faux-fullscreen-close" v-if="isFauxFullscreen" @click="toggleFullscreen">
						<span class="icon is-medium"><i class="fa fa-times"></i></span>
					</div>
				</div>
			</div>
			<div class="margin top slideshow-menu">
				<div class="slideshow-annotations" v-if="!isLoading">
					<wnl-annotations
						:currentSlide="currentSlideNumber"
						:slideshowId="slideshowId"
						@commentsHidden="onCommentsHidden"
					></wnl-annotations>
				</div>
				<div class="slideshow-fullscreen">
					<wnl-image-button name="wnl-slideshow-control-fullscreen"
						icon="fullscreen-arrows"
						alt="Włącz pełen ekran"
						align="right"
						title="Pełen ekran"
						@buttonclicked="toggleFullscreen"
					></wnl-image-button>
				</div>
			</div>
		</div>
	</div>
</template>
<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

	.wnl-slideshow-background-control
		align-items: center
		border-top: $border-light-gray
		color: $color-gray-dimmed
		display: flex
		font-size: $font-size-minus-2
		justify-content: space-between
		line-height: $line-height-plus
		margin: $margin-base 0
		padding-top: $margin-base
		text-align: right
		text-transform: uppercase
		vertical-align: middle

		.controls-item
			align-items: center
			display: flex

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

	.slideshow-menu
		display: flex

		.slideshow-annotations
			flex: 1 auto

		.slideshow-fullscreen
			flex: 0
			padding-left: $margin-base
</style>

<script>
	import _ from 'lodash'
	import Postmate from 'postmate'
	import screenfull from 'screenfull'
	import {mapGetters, mapActions} from 'vuex'
	import {scrollToTop} from 'js/utils/animations'

	import Annotations from './Annotations'
	import SlideshowNavigation from './SlideshowNavigation'
	import {isDebug, getUrl} from 'js/utils/env'

	let debounced;

	export default {
		name: 'Slideshow',
		components: {
			'wnl-annotations': Annotations,
			'wnl-slideshow-navigation': SlideshowNavigation,
		},
		data() {
			return {
				child: {},
				currentSlideNumber: Math.max(this.$route.params.slide, 1) || 1,
				loaded: false,
				isFullscreen: false,
				isFauxFullscreen: false,
				isFocused: false,
				slideChanged: false
			}
		},
		props: ['screenData', 'slide'],
		computed: {
			...mapGetters(['getSetting']),
			...mapGetters('slideshow', [
				'isLoading',
				'isFunctional',
				'findRegularSlide',
			]),
			currentSlideIndex() {
				return this.currentSlideNumber - 1
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
			...mapActions('slideshow', ['setup']),
			toggleFullscreen() {
				if (screenfull.enabled) {
					screenfull.toggle(this.slideshowElement)
				} else {
					this.isFauxFullscreen = !this.isFauxFullscreen
				}
				this.focusSlideshow()
			},
			setCurrentSlideFromIndex(index) {
				this.currentSlideNumber = this.slideNumberFromIndex(index)
			},
			slideNumberFromIndex(index) {
				return index + 1
			},
			goToSlide(slideIndex) {
				this.slideChanged = true
				this.child.call('goToSlide', slideIndex)
				this.setCurrentSlideFromIndex(slideIndex)
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

						this.goToSlide(this.currentSlideIndex)
						this.focusSlideshow()
					}).catch(exception => $wnl.logger.capture(exception))
			},
			messageEventListener(event) {
				if (typeof event.data === 'string' && event.data.indexOf('reveal') > -1) {
					try {
						let data = JSON.parse(event.data)
						if (data.namespace === 'reveal' &&
							data.eventName === 'slidechanged' &&
							this.slideChanged === false
						) {
							let currentSlideNumber = this.slideNumberFromIndex(data.state.indexh)
							this.currentSlideNumber = currentSlideNumber
							this.$router.replace({
								name: 'screens',
								params: { slide: currentSlideNumber }
							})
							this.focusSlideshow()
						}

						this.slideChanged = false
					} catch (error) { $wnl.logger.error(error) }
				}
			},
			setEventListeners() {
				debounced = _.debounce(
					this.messageEventListener.bind(this),
					100,
					{
						leading: true,
						trailing: true,
					}
				)

				addEventListener('message', debounced)
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
				removeEventListener('message', debounced)
				this.loaded = false
			},
			onCommentsHidden() {
				this.focusSlideshow()
				scrollToTop()
			},
		},
		mounted() {
			Postmate.debug = false
			this.initSlideshow()
			this.setup(this.slideshowId)
		},
		beforeDestroy() {
			this.destroySlideshow()
		},
		watch: {
			'$route' (to, from) {
				if (to.params.screenId !== from.params.screenId) {
					this.destroySlideshow()
				}

				let fromSlide = from.params.slide || 0,
					toSlide = to.params.slide

				if (this.loaded && !_.isUndefined(toSlide)) {
					if (this.getSetting('skip_functional_slides') && !!this.isFunctional(toSlide)) {
						let direction = toSlide > fromSlide ? 'next' : 'previous',
							skipTo = this.findRegularSlide(toSlide, direction)
						this.goToSlide(skipTo - 1)
					} else if (toSlide !== this.currentSlideNumber) {
						this.goToSlide(toSlide - 1)
					}
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
