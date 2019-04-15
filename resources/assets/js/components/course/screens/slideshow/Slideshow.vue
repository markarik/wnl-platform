<template>
	<div>
		<div class="wnl-slideshow-container">
			<div class="wnl-slideshow-background-control">
				<div class="controls-left">
					<wnl-slideshow-navigation @navigateToSlide="navigateToSlide"></wnl-slideshow-navigation>
				</div>
				<small v-if="$moderatorFeatures.isAllowed('access')" class="slide-meta">
					{{currentSlideId}}
					<wnl-linked-questions :slide-id="currentSlideId" />
				</small>
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
			<div class="slideshow-menu">
				<wnl-annotations
					:slideshow-id="presentableId"
					:screen-id="Number(screenId)"
					:current-slide-id="currentSlideId"
					:is-loading-comments="isLoadingComments"
					@commentsHidden="onCommentsHidden"
					@annotationsUpdated="onAnnotationsUpdated"
				></wnl-annotations>
			</div>
		</div>
		<wnl-slide-classifier-editor
			:current-slide-id="currentSlideId"
		/>
	</div>
</template>
<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

	.wnl-slideshow-background-control
		align-items: center
		color: $color-gray
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
				background: $color-darkest-gray

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
		border: $border-light-gray
		margin-top: -3px
		padding-top: $margin-base

	.slide-meta
		text-align: center
</style>

<script>
import _ from 'lodash';
import axios from 'axios';
import Postmate from 'postmate';
import screenfull from 'screenfull';
import { mapGetters, mapActions, mapMutations } from 'vuex';
import { scrollToTop } from 'js/utils/animations';
import features from 'js/consts/events_map/features.json';

import * as types from 'js/store/mutations-types';
import emits_events from 'js/mixins/emits-events';
import Annotations from './Annotations';
import LinkedQuestions from './LinkedQuestions.vue';
import SlideshowNavigation from './SlideshowNavigation';
import { isDebug, getApiUrl } from 'js/utils/env';
import moderatorFeatures from 'js/perimeters/moderator';
import WnlSlideClassifierEditor from 'js/components/course/screens/slideshow/SlideClassifierEditor';
import { USER_SETTING_NAMES } from 'js/consts/settings';

export default {
	name: 'Slideshow',
	components: {
		'wnl-annotations': Annotations,
		'wnl-linked-questions': LinkedQuestions,
		'wnl-slideshow-navigation': SlideshowNavigation,
		WnlSlideClassifierEditor,
	},
	perimeters: [moderatorFeatures],
	mixins: [emits_events],
	data() {
		return {
			bookmarkLoading: false,
			child: {},
			currentSlideId: 0,
			// slides order number is index from 0
			currentSlideNumber: this.slideOrderNumber + 1 || Math.max(this.$route.params.slide, 1) || 1,
			isFauxFullscreen: false,
			isFocused: false,
			loaded: false,
			slideChanged: false,
			slideshowElement: {},
			modifiedSlides: {},
		};
	},
	props: {
		screenData: {
			type: Object,
			default: () => {
				return {
					type: 'slideshow',
					meta: {
						resources: [{
							id: -1
						}]
					}
				};
			}
		},
		preserveRoute: Boolean,
		slideOrderNumber: Number,
		htmlContent: String
	},
	computed: {
		...mapGetters(['getSetting', 'currentUserId']),
		...mapGetters('slideshow', [
			'comments',
			'commentProfile',
			'isLoading',
			'isFunctional',
			'findRegularSlide',
			'getSlidePositionById',
			'getSlideIdFromIndex',
			'getSlideById',
			'presentableSortedSlidesIds',
			'isLoadingComments'
		]),
		currentSlideIndex() {
			return this.currentSlideNumber - 1;
		},
		container() {
			return this.$el.getElementsByClassName('wnl-slideshow-content')[0];
		},
		screenId() {
			return this.$route.params.screenId;
		},
		presentableId() {
			return this.screenData.meta && this.screenData.meta.resources[0].id;
		},
		presentableType() {
			return this.screenData.type;
		},
		slideshowUrl() {
			return getApiUrl(`slideshow_builder/${this.presentableType === 'category' ? 'category/' : ''}${this.presentableId}`);
		},
		iframe() {
			return this.loaded ? this.$el.getElementsByTagName('iframe')[0] : null;
		},
	},
	methods: {
		...mapActions('slideshow', ['setup', 'resetModule', 'setSortedSlidesIds', 'setupSlideComments']),
		...mapActions(['toggleOverlay', 'showNotification']),
		...mapMutations('slideshow', {
			loadingComments: types.SLIDESHOW_LOADING_COMMENTS
		}),
		toggleBookmarkedState(slideIndex) {
			this.bookmarkLoading = true;

			const slideId = this.getSlideIdFromIndex(slideIndex);
			const slide = this.getSlideById(slideId);
			const currentBookmarkState = slide.bookmark.hasReacted;

			return this.$store.dispatch('slideshow/setReaction', {
				hasReacted: currentBookmarkState,
				reactableResource: 'slides',
				reactableId: slideId,
				reaction: 'bookmark',
				count: slide.bookmark.count,
			}).then(() => {
				this.child.call('setBookmarkState', slide.bookmark.hasReacted);
				this.$emit('slideBookmarked', { slideId, hasReacted: slide.bookmark.hasReacted });
			}).then(() => {
				this.bookmarkLoading = false;
			}).catch(() => {
				this.bookmarkLoading = false;
			});
		},
		toggleFullscreen() {
			if (screenfull.enabled) {
				screenfull.toggle(this.slideshowElement);
			} else {
				this.isFauxFullscreen = !this.isFauxFullscreen;
				this.child.call('toggleFullscreen', this.isFauxFullscreen);
			}
			// there is negation before screenfull.isFullscreen because it's returning the opposite value...
			const fullscreenValue = screenfull.enabled ? !screenfull.isFullscreen : this.isFauxFullscreen;
			this.emitUserEvent({
				action: features.slideshow.feature_components.slide.actions.toggle_fullscreen.value,
				feature: features.slideshow.value,
				feature_component: features.slideshow.feature_components.slide.value,
				target: this.currentSlideId,
				value: Number(fullscreenValue)
			});
			this.focusSlideshow();
		},
		slideNumberFromIndex(index) {
			return index + 1;
		},
		goToSlide(slideIndex) {
			if (typeof slideIndex !== 'undefined' && slideIndex > -1) {
				this.slideChanged = true;

				const newSlideId = this.getSlideIdFromIndex(slideIndex);
				const slide = this.getSlideById(newSlideId);
				const orderNumber = this.getSlidePositionById(newSlideId);

				this.child.call('goToSlide', slideIndex);
				this.child.call('setBookmarkState', slide.bookmark.hasReacted);
				this.child.call('setSlideOrderNumber', this.slideNumberFromIndex(orderNumber));

				this.currentSlideId = newSlideId;
				this.currentSlideNumber = this.slideNumberFromIndex(slideIndex);

				this.focusSlideshow();
			}
		},
		changeBackground(background = 'image') {
			this.child.call('changeBackground', background);
			this.focusSlideshow();
		},
		focusSlideshow() {
			if (typeof this.child !== 'undefined' &&
				this.child.hasOwnProperty('frame') &&
				typeof this.child.frame !== 'undefined'
			) {
				this.child.frame.click();
				this.child.frame.focus();
			}
			this.isFocused = true;
		},
		checkFocus() {
			this.isFocused = this.iframe === document.activeElement;
		},
		initSlideshow(slideshowUrl = this.slideshowUrl) {
			this.toggleOverlay({ source: 'slideshow', display: true });

			this.setSortedSlidesIds(this.presentableSortedSlidesIds);

			const postmateOptions = {
				container: this.container,
				url: slideshowUrl,
			};

			return this.postmateHandshake(postmateOptions)
				.then(() => {
					if (this.$route.query.slide) {
						const newSlideIndex = this.presentableSortedSlidesIds.indexOf(Number(this.$route.query.slide));
						if (newSlideIndex > -1) {
							this.goToSlide(newSlideIndex);
							this.$router.push(this.buildRouteFromSlideParam(newSlideIndex));
						} else {
							this.goToSlide(this.currentSlideIndex);
						}
					} else {
						this.goToSlide(this.currentSlideIndex);
					}
					this.focusSlideshow();
					this.loaded = true;
					this.currentSlideId = this.getSlideIdFromIndex(this.currentSlideIndex);
					this.debouncedTrackEvent({
						target: this.currentSlideId
					});
					this.toggleOverlay({ source: 'slideshow', display: false });
				})
				.catch(error => {
					this.toggleOverlay({ source: 'slideshow', display: false });
					$wnl.logger.capture(error);
				});
		},

		setSlideshowHtmlContent(htmlContent) {
			this.toggleOverlay({ source: 'slideshow', display: true });

			const postmateOptions = {
				container: this.container,
				targetOrigin: window.location.href,
				srcdoc: htmlContent
			};

			return this.postmateHandshake(postmateOptions)
				.then(() => {
					this.goToSlide(this.currentSlideIndex);

					this.slideChanged = false;
					this.loaded = true;
					this.toggleOverlay({ source: 'slideshow', display: false });
					this.child.call('refreshChart', this.currentSlideIndex);
					this.currentSlideId = this.getSlideIdFromIndex(this.currentSlideIndex);
					this.debouncedTrackEvent({
						target: this.currentSlideId
					});
				})
				.catch(error => {
					this.toggleOverlay({ source: 'slideshow', display: false });
					$wnl.logger.capture(error);
				});

		},

		postmateHandshake(options) {
			return new Promise((resolve, reject) => {
				new Postmate(options)
					.then(child => {
						this.child = child;
						this.slideshowElement = this.container.getElementsByTagName('iframe')[0];
						this.setEventListeners();
						child.frame.setAttribute('mozallowfullscreen', '');
						child.frame.setAttribute('allowfullscreen', '');

						child.call('setDebug', isDebug());

						return resolve();
					})
					.catch(reject);
			});
		},
		updateRoute(slideNumber) {
			!this.preserveRoute && this.$router.replace({
				name: 'screens',
				params: { slide: slideNumber }
			});
		},
		navigateToSlide(slideNumber) {
			this.preserveRoute ? this.goToSlide(slideNumber - 1) : this.updateRoute(slideNumber);
		},
		messageEventListener(event) {
			if (typeof event.data === 'string' && event.data.indexOf('reveal') > -1) {
				try {
					let data = JSON.parse(event.data);
					if (data.namespace === 'reveal' &&
						data.eventName === 'slidechanged' &&
						this.slideChanged === false
					) {
						const currentSlideNumber = this.slideNumberFromIndex(data.state.indexh);
						const slideId = this.getSlideIdFromIndex(data.state.indexh);
						const slide = this.getSlideById(slideId);
						const orderNumber = this.getSlidePositionById(slideId);


						this.currentSlideNumber = currentSlideNumber;
						this.currentSlideId = slideId;
						this.updateRoute(currentSlideNumber);
						this.focusSlideshow();

						this.child.call('setBookmarkState', slide.bookmark.hasReacted);
						this.child.call('setSlideOrderNumber', this.slideNumberFromIndex(orderNumber));

						this.debouncedTrackEvent({
							target: slideId
						});
					}

					this.slideChanged = false;
				} catch (error) {
					$wnl.logger.error(error);
				}
			} else if (typeof event.data === 'object' &&
				event.data.hasOwnProperty('value')
			) {
				if (event.data.value.name === 'toggle-fullscreen') {
					this.toggleFullscreen();
				} else if (event.data.value.name === 'loaded') {
					this.toggleOverlay({ source: 'slideshow', display: false });
				} else if (event.data.value.name === 'bookmark') {
					const { index } = event.data.value.data;

					!this.bookmarkLoading && this.toggleBookmarkedState(index);
				} else if (event.data.value.name === 'error') {
					this.toggleOverlay({ source: 'slideshow', display: false });
				} else if (event.data.value.name === 'refresh-slideshow') {
					if (this.presentableType === 'category') {
						this.$emit('refreshSlideshow');
						screenfull.exit(this.slideshowElement);
					} else {
						this.onRefreshSlideshow();
					}
					this.modifiedSlides = {};
				} else if (event.data.value.name === 'navigate') {
					window.open(event.data.value.data);
				} else if (event.data.value.name === 'global-shortcut-key') {
					document.dispatchEvent(
						new KeyboardEvent('keydown', { key: event.data.value.data })
					);
				}
			}
		},
		fullscreenChangeHandler() {
			this.child.call('toggleFullscreen', screenfull.isFullscreen);
		},
		keydownNavigationHandler(event) {
			if (this.$shortcutKeyIsEditable(event.target)) {
				return;
			}

			switch (event.key) {
			case ']':
				this.navigateToSlide(this.currentSlideNumber + 1);
				break;
			case '[':
				this.navigateToSlide(this.currentSlideNumber - 1);
				break;
			}
		},
		debouncedMessageListener: _.debounce(function (event) {
			this.messageEventListener(event);
		}, {
			trailing: true,
		}),
		debouncedTrackEvent: _.debounce(function (payload) {
			this.emitUserEvent({
				action: features.slideshow.feature_components.slide.actions.open.value,
				feature: features.slideshow.value,
				feature_component: features.slideshow.feature_components.slide.value,
				...payload
			});
		}),
		setEventListeners() {
			addEventListener('fullscreenchange', this.fullscreenChangeHandler, false);
			addEventListener('webkitfullscreenchange', this.fullscreenChangeHandler, false);
			addEventListener('mozfullscreenchange', this.fullscreenChangeHandler, false);
			document.addEventListener('keydown', this.keydownNavigationHandler, false);

			addEventListener('message', this.debouncedMessageListener);
			addEventListener('blur', this.checkFocus);
			addEventListener('focus', this.checkFocus);
			addEventListener('focusout', this.checkFocus);
		},
		removeEventListeners() {
			removeEventListener('fullscreenchange', this.fullscreenChangeHandler, false);
			removeEventListener('webkitfullscreenchange', this.fullscreenChangeHandler, false);
			removeEventListener('mozfullscreenchange', this.fullscreenChangeHandler, false);
			document.removeEventListener('keydown', this.keydownNavigationHandler, false);

			removeEventListener('blur', this.checkFocus);
			removeEventListener('focus', this.checkFocus);
			removeEventListener('focusout', this.checkFocus);
			removeEventListener('message', this.debouncedMessageListener);

		},
		destroySlideshow() {
			this.toggleOverlay({ source: 'slideshow', display: false });
			if (typeof this.child.destroy === 'function') {
				this.child.destroy();
			}

			this.removeEventListeners();
			this.resetModule();
			this.loaded = false;
		},
		onAnnotationsUpdated(comments) {
			if (typeof this.child !== 'undefined' && typeof this.child.call === 'function') {
				const annotations = _.cloneDeep(comments);

				if (annotations.length > 0) {
					annotations.forEach((annotation) => {
						annotation.profiles = annotation.profiles.map((id) => {
							return this.commentProfile(id);
						});
					});
				}

				this.child.call('updateAnnotations', annotations);
			}
		},
		onCommentsHidden() {
			this.focusSlideshow();
			scrollToTop();
		},
		buildRouteFromSlideParam(index) {
			return {
				...this.$route,
				params: {
					...this.$route.params,
					slide: this.slideNumberFromIndex(index)
				},
				query: {
					...this.$route.query
				}
			};
		},
		setupCollection() {
			return this.setSlideshowHtmlContent(this.htmlContent).catch((error) => {
				this.toggleOverlay({ source: 'slideshow', display: false });
				$wnl.logger.capture(error);
			});
		},
		onRefreshSlideshow() {
			this.toggleOverlay({ source: 'slideshow', display: true });
			this.removeEventListeners();

			Promise.all([
				axios.get(this.slideshowUrl),
				this.setup({ id: this.presentableId })
			]).then(([{ data }]) => {
				if (typeof this.child.destroy === 'function') {
					this.child.destroy();
				}
				this.setSortedSlidesIds(this.presentableSortedSlidesIds);
				this.setSlideshowHtmlContent(data)
					.then(() => {
						const slide = this.getSlideById(this.currentSlideId);
						this.child.call('setBookmarkState', slide.bookmark.hasReacted);
					});
			});
		},
		changeSlideWatcher(currentSlideId, previousSlideId) {
			this.setupSlideComments({ id: currentSlideId });

			Echo.channel(`commentable-slide-${currentSlideId}`)
				.listen('.App.Events.Live.LiveContentUpdated', async ({ data: { event, subject } }) => {
					switch (event) {
					case 'comment-posted':
						await this.setupSlideComments({ commentable_id: currentSlideId, comment_id: subject.id });
						break;
					}
				});

			Echo.leave(`commentable-slide-${previousSlideId}`);
		},
		debouncedChangeSlideWatcher: _.debounce(function (...args) {
			this.changeSlideWatcher(...args);
		}, 300, { leading: false, trailing: true }),
	},
	mounted() {
		Echo.channel(`presentable-${this.presentableType}-${this.presentableId}`)
			.listen('.App.Events.Live.LiveContentUpdated', ({ data: { event, subject, context } }) => {
				switch (event) {
				case 'slide-added':
					// TODO consider passing order_number in given presentable from event
					this.modifiedSlides[subject.id] = { order_number: context.params.slide - 1, action: 'add' };
					this.child.call('updateModifiedSlides', Object.values(this.modifiedSlides));
					break;
				case 'slide-updated':
					this.modifiedSlides[subject.id] = { ...this.getSlideById(subject.id), action: 'edit' };
					this.child.call('updateModifiedSlides', Object.values(this.modifiedSlides));
					break;
				case 'slide-detached':
					this.modifiedSlides[subject.id] = { ...this.getSlideById(subject.id), action: 'delete' };
					this.child.call('updateModifiedSlides', Object.values(this.modifiedSlides));
					break;
				}
			});

		Postmate.debug = isDebug();
		this.toggleOverlay({ source: 'slideshow', display: true });
		if (this.htmlContent) {
			// logic related with category / collection
			this.setupCollection();
		} else {
			// logic related with lesson
			this.setup({ id: this.presentableId })
				.then(() => {
					return this.initSlideshow();
				})
				.catch(error => {
					this.toggleOverlay({ source: 'slideshow', display: false });
					$wnl.logger.capture(error);
				});
		}
	},
	beforeDestroy() {
		this.destroySlideshow();
	},
	watch: {
		'$route'(to, from) {
			if (to.params.screenId != from.params.screenId) {
				return this.destroySlideshow();
			}

			if (to.params.categoryName != from.params.categoryName) {
				return this.destroySlideshow();
			}

			if (to.query.slide && to.query.slide !== this.currentSlideId) {
				const newSlideIndex = this.presentableSortedSlidesIds.indexOf(Number(this.$route.query.slide));
				if (newSlideIndex > -1) {
					this.goToSlide(newSlideIndex);
					this.$router.push(this.buildRouteFromSlideParam(newSlideIndex));
				}
			}

			if (to.query.slide === this.currentSlideId) {
				const newSlideIndex = this.presentableSortedSlidesIds.indexOf(Number(this.$route.query.slide));
				if (newSlideIndex > -1) {
					this.$router.push(this.buildRouteFromSlideParam(newSlideIndex));
				}
			}

			let fromSlide = from.params.slide || 0,
				toSlide = to.params.slide;

			if (this.loaded && !_.isUndefined(toSlide)) {
				if (this.getSetting(USER_SETTING_NAMES.SKIP_FUNCTIONAL_SLIDES) && !!this.isFunctional(toSlide)) {
					let direction = toSlide > fromSlide ? 'next' : 'previous',
						skipTo = this.findRegularSlide(toSlide, direction);
					this.goToSlide(skipTo - 1);
				} else if (toSlide !== this.currentSlideNumber) {
					this.goToSlide(toSlide - 1);
				}
			}
		},
		'htmlContent'(newContent) {
			if (typeof this.child.destroy === 'function') {
				this.child.destroy();
			}

			this.removeEventListeners();
			this.setSlideshowHtmlContent(newContent);
			this.modifiedSlides = {};
		},
		'screenData'(newValue, oldValue) {
			if (newValue.type === 'slideshow' && newValue.id !== oldValue.id) {
				this.toggleOverlay({ source: 'slideshow', display: true });

				this.setup({ id: this.presentableId })
					.then(() => {
						this.initSlideshow()
							.then(() => {
								this.goToSlide(Math.max(this.$route.params.slide - 1, 0));
							})
							.catch(error => {
								this.toggleOverlay({ source: 'slideshow', display: false });
								$wnl.logger.capture(error);
							});
					})
					.catch(error => {
						this.toggleOverlay({ source: 'slideshow', display: false });
						$wnl.logger.capture(error);
					});
			}
		},
		'slideOrderNumber'(slideOrderNumber) {
			typeof this.child.call === 'function' && this.goToSlide(slideOrderNumber);
		},
		isLoadingComments(isLoadingComments) {
			if (!isLoadingComments) {
				this.onAnnotationsUpdated(this.comments({
					resource: 'slides',
					id: this.getSlideIdFromIndex(this.currentSlideIndex),
				}));
			}
		},
		currentSlideId(...args) {
			this.loadingComments(true);
			this.debouncedChangeSlideWatcher(...args);
		},
	}
};
</script>
