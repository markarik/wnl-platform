<template>
	<div>
		<div class="wnl-slideshow-container">
			<div class="wnl-slideshow-background-control">
				<div class="controls-left">
					<wnl-slideshow-navigation @navigateToSlide="navigateToSlide"></wnl-slideshow-navigation>
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
				</div>
			</div>
			<div class="margin top slideshow-menu">
				<wnl-annotations
					v-if="!isLoading"
					:currentSlide="currentSlideNumber"
					:slideshowId="slideshowId"
					@commentsHidden="onCommentsHidden"
					@annotationsUpdated="onAnnotationsUpdated"
				></wnl-annotations>
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

		.slideshow-fullscreen
			flex: 0
			padding-left: $margin-base
</style>

<script>
	import _ from 'lodash'
	import Postmate from 'postmate-fork'
	import screenfull from 'screenfull'
	import {mapGetters, mapActions} from 'vuex'
	import {scrollToTop} from 'js/utils/animations'

	import Annotations from './Annotations'
	import SlideshowNavigation from './SlideshowNavigation'
	import {isDebug, getApiUrl} from 'js/utils/env'

	let debounced, handshake

	export default {
		name: 'Slideshow',
		components: {
			'wnl-annotations': Annotations,
			'wnl-slideshow-navigation': SlideshowNavigation,
		},
		data() {
			return {
				child: {},
				currentSlideNumber: this.slideOrderNumber || Math.max(this.$route.params.slide, 1) || 1,
				loaded: false,
				isFauxFullscreen: false,
				isFocused: false,
				slideChanged: false,
				currentSlideId: 0,
				bookmarkLoading: false
			}
		},
		props: ['screenData', 'presentableId', 'presentableType', 'preserveRoute', 'slideOrderNumber'],
		computed: {
			...mapGetters(['getSetting']),
			...mapGetters('slideshow', [
				'comments',
				'commentProfile',
				'getSlideId',
				'isLoading',
				'isFunctional',
				'findRegularSlide',
				'presentables',
				'bookmarkedSlideNumbers',
				'getReaction'
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
				return this.screenData.meta && this.screenData.meta.resources[0].id
			},
			slideshowUrl() {
				return getApiUrl(`slideshow_builder/${this.slideshowId}`)
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
			bookmarkState() {
				return this.getReaction('slides', this.currentSlideId, 'bookmark')
			}
		},
		methods: {
			...mapActions('slideshow', ['setup', 'setupPresentables', 'setupByPresentable']),
			...mapActions(['toggleOverlay']),
			toggleBookmarkedState(slideIndex, hasReacted) {
				this.bookmarkLoading = true

				return this.$store.dispatch(`slideshow/setReaction`, {
					hasReacted,
					reactableResource: 'slides',
					reactableId: this.getSlideId(slideIndex),
					reaction: 'bookmark'
				}).then(() => {
					const hasReacted = !this.bookmarkState.hasReacted
					const count = hasReacted ? this.bookmarkState.count + 1 : this.bookmarkState.count - 1;

					this.$store.commit('slideshow/SET_REACTION', {
						count,
						hasReacted,
						reactableResource: 'slides',
						reactableId: this.getSlideId(slideIndex),
						reaction: 'bookmark',
					})

					return Promise.resolve({
						hasReacted
					})
				}).then((data) => {
					this.child.call('setBookmarkedState', data.hasReacted)
				}).then(() => {
					this.bookmarkLoading = false
				})
			},
			toggleFullscreen() {
				if (screenfull.enabled) {
					screenfull.toggle(this.slideshowElement)
				} else {
					this.isFauxFullscreen = !this.isFauxFullscreen
					this.child.call('toggleFullscreen', this.isFauxFullscreen)
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
				if (this.child.hasOwnProperty('frame') && typeof this.child.frame !== undefined) {
					this.child.frame.click()
					this.child.frame.focus()
				}
				this.isFocused = true
			},
			checkFocus() {
				this.isFocused = this.iframe === document.activeElement
			},
			initSlideshow(slideshowUrl) {
				this.toggleOverlay({source: 'slideshow', display: true})
				handshake = new Postmate({
					container: this.container,
					url: slideshowUrl || this.slideshowUrl,
				})

				return handshake.then(child => {
					this.child = child
					this.loaded = true
					child.frame.setAttribute('mozallowfullscreen', '');
					child.frame.setAttribute('allowfullscreen', '');
					this.setEventListeners()

					this.goToSlide(this.currentSlideIndex)
					this.focusSlideshow()

					this.onAnnotationsUpdated(this.comments({
						resource: 'slides',
						id: this.getSlideId(this.currentSlideIndex),
					}))
				}).catch(exception => $wnl.logger.capture(exception))
			},
			updateRoute(slideNumber) {
				!this.preserveRoute && this.$router.replace({
					name: 'screens',
					params: { slide: slideNumber }
				})
			},
			navigateToSlide(slideNumber) {
				this.preserveRoute ? this.goToSlide(slideNumber - 1) : this.updateRoute(slideNumber)
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
							this.updateRoute(currentSlideNumber)
							this.focusSlideshow()
						}

						this.slideChanged = false
					} catch (error) { $wnl.logger.error(error) }
				} else if (typeof event.data === 'object' &&
					event.data.hasOwnProperty('value')
				) {
					if (event.data.value.name === 'toggle-fullscreen') {
						this.toggleFullscreen()
					} else if (event.data.value.name === 'loaded') {
						this.toggleOverlay({source: 'slideshow', display: false})
						this.child.call('setupBookmarks', this.bookmarkedSlideNumbers);
					} else if (event.data.value.name === 'bookmark') {
						const slideData = event.data.value.data;

						!this.bookmarkLoading && this.toggleBookmarkedState(slideData.index, slideData.isBookmarked);
					}
				}
			},
			fullscreenChangeHandler(event) {
				this.child.call('toggleFullscreen', screenfull.isFullscreen)
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

				addEventListener('fullscreenchange', this.fullscreenChangeHandler, false);
				addEventListener('webkitfullscreenchange', this.fullscreenChangeHandler, false);
				addEventListener('mozfullscreenchange', this.fullscreenChangeHandler, false);

				addEventListener('message', debounced)
				addEventListener('blur', this.checkFocus)
				addEventListener('focus', this.checkFocus)
				addEventListener('focusout', this.checkFocus)
			},
			destroySlideshow() {
				if (typeof this.child.destroy === 'function') {
					this.child.destroy()
				}

				removeEventListener('fullscreenchange', this.fullscreenChangeHandler, false);
				removeEventListener('webkitfullscreenchange', this.fullscreenChangeHandler, false);
				removeEventListener('mozfullscreenchange', this.fullscreenChangeHandler, false);

				removeEventListener('blur', this.checkFocus)
				removeEventListener('focus', this.checkFocus)
				removeEventListener('focusout', this.checkFocus)
				removeEventListener('message', debounced)
				this.loaded = false
			},
			onAnnotationsUpdated(comments) {
				if (typeof this.child.call === 'function') {
					let annotations = _.cloneDeep(comments)

					if (annotations.length > 0) {
						annotations.forEach((annotation) => {
							annotation.profiles = annotation.profiles.map((id) => {
								return this.commentProfile(id)
							})
						})
					}

					this.child.call('updateAnnotations', annotations)
				}
			},
			onCommentsHidden() {
				this.focusSlideshow()
				scrollToTop()
			},
		},
		mounted() {
			Postmate.debug = isDebug()
			if (this.presentableId || this.presentableType) {
				this.setupByPresentable({type: this.presentableType, id: this.presentableId})
				.then(() => {
					this.presentableId && this.initSlideshow(getApiUrl(`slideshow_builder/category/${this.presentableId}`))
					.then(() => {
						this.goToSlide(this.slideOrderNumber - 1)
					})
					this.currentSlideId = this.getSlideId(this.currentSlideIndex)
				})
			} else {
				this.setup(this.slideshowId)
				.then(() => {
					this.initSlideshow()
					this.currentSlideId = this.getSlideId(this.currentSlideIndex)
				})
			}
		},
		beforeDestroy() {
			this.destroySlideshow()
		},
		watch: {
			'$route' (to, from) {
				if (to.params.screenId != from.params.screenId) {
					this.destroySlideshow()
				}

				if (to.params.categoryName != from.params.categoryName) {
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
			'presentableId' (newValue, oldValue) {
				newValue && this.initSlideshow(getApiUrl(`slideshow_builder/category/${newValue}`))
			},
			'currentSlideIndex' (newValue, oldValue) {
				this.currentSlideId = this.getSlideId(newValue)
			},
			'slideOrderNumber' (newValue, oldValue) {
				console.log('slideOrderNumber...', newValue)
				typeof this.child.call === 'function' && this.goToSlide(newValue--)
			}
		}
	}
</script>
