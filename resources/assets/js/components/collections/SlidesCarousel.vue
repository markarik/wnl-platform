<template>
	<div class="wnl-slides-collection">
		<p class="title is-4">Zapisane slajdy <span v-if="!!savedSlidesCount">({{savedSlidesCount}})</span>
			<a class="saved-slides-toggle panel-toggle" :class="{'is-active': mode === 'bookmarked'}" @click="toggleBookmarked()">
					Pokaż tylko zapisane slajdy
					<span class="icon is-small">
						<i class="fa" :class="[mode === 'bookmarked' ? 'fa-check-circle' : 'fa-circle-o']"></i>
					</span>
			</a>
		</p>
		<div class="slides-carousel-container" v-if="!!savedSlidesCount">
			<div class="slides-carousel">
				<div class="slide-thumb" :key="index" v-for="(slide, index) in sortedSlides" @click="showSlide(index)">
					<div class="thumb-meta">
						<span class="thumb-slide-number">{{getSlideDisplayNumberFromIndex(index)}}</span>
						<span class="icon is-tiny" v-if="slide.media"><i class="fa" :class="slide.media.icon"></i></span>
					</div>
					<p class="thumb-heading metadata">{{slide.header}}</p>
					<div class="slide-snippet" v-html="slide.snippet"></div>
					<div class="slide-snippet has-media" v-if="slide.media">
						<span class="icon is-tiny">
							<i class="fa" :class="slide.media.icon"></i>
						</span>
						{{slide.media.text}}
					</div>
					<div class="shadow"></div>
				</div>
			</div>
		</div>
		<div v-else-if="!savedSlidesCount" class="notification has-text-centered">
			W temacie <span class="metadata">{{rootCategoryName}} <span class="icon is-small"><i class="fa fa-angle-right"></i></span> {{categoryName}}</span> nie ma jeszcze zapisanych slajdów. Możesz łatwo to zmienić klikając na <span class="icon is-small"><i class="fa fa-star-o"></i></span> <span class="metadata">ZAPISZ</span> na wybranym slajdzie!
		</div>
		<wnl-slideshow
			ref="slideshow"
			v-if="htmlContent"
			:htmlContent="htmlContent"
			:preserveRoute="true"
			:slideOrderNumber="currentSlideOrderNumber"
			@slideBookmarked="onSlideBookmarked"
		></wnl-slideshow>
	</div>
</template>
<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

	$carousel-height: 105px
	$thumb-height: 90px
	$thumb-width: 160px

	.is-not-visible
		visibility: hidden

	.wnl-slides-collection
		margin-top: $margin-base

	.wnl-carousel
		justify-content: space-around
		margin: $margin-base 0

	.VueCarousel-inner
		margin: 0 auto

	.slides-carousel-container
		+scrollbar(15px, $color-light-gray, $color-background-lighter-gray)
		+scrollbar-body($color-light-gray, $color-background-lighter-gray)

		height: $carousel-height
		margin-bottom: $margin-base
		position: relative

	.saved-slides-toggle
		float: right
		margin: 0

	.slides-carousel
		background: $color-background-lighter-gray
		display: flex
		left: 0
		overflow-y: hidden
		overflow-x: auto
		position: absolute
		top: 0
		width: 100%

	.slide-thumb
		background-color: $color-white
		cursor: pointer
		flex: 1 0 $thumb-width
		height: $thumb-height
		margin: $margin-small $margin-small $margin-base
		max-width: $thumb-width
		overflow: hidden
		padding: $margin-small
		position: relative
		text-align: center
		transition: color $transition-length-base
		width: $thumb-width

		&:hover
			color: $color-ocean-blue
			transition: color $transition-length-base

			.shadow
				height: 0
				transition: height $transition-length-base

		.thumb-meta
			align-items: center
			display: flex
			justify-content: space-between

		.thumb-slide-number
			font-size: $font-size-minus-3
			line-height: $line-height-minus
			margin-bottom: $margin-tiny
			text-align: left

		.thumb-heading
			line-height: $line-height-minus
			margin-bottom: $margin-small

		.slide-snippet
			font-size: $font-size-minus-2
			line-height: $line-height-minus

			&.has-media
				margin-top: $margin-small

		.shadow
			+white-shadow-inside()

			bottom: 0
			height: 50%
			position: absolute
			transition: height $transition-length-base
			width: 100%
</style>

<script>
	import { mapActions, mapGetters } from 'vuex'
	import Slideshow from 'js/components/course/screens/slideshow/Slideshow.vue'
	import {getApiUrl} from 'js/utils/env'

	const mediaMap = {
		chart: {
			icon: 'fa-sitemap',
			text: 'Diagram',
		},
		movie: {
			icon: 'fa-film',
			text: 'Film',
		},
		audio: {
			icon: 'fa-music',
			text: 'Nagranie audio',
		},
	}

	export default {
		name: 'SlidesCarousel',
		props: ['categoryId', 'rootCategoryName', 'categoryName', 'savedSlidesCount'],
		data() {
			return {
				presentableType: 'App\\Models\\Category',
				selectedSlideIndex: 0,
				htmlContent: '',
				loadedHtmlContents: {},
				mode: 'bookmarked'
			}
		},
		components: {
			'wnl-slideshow': Slideshow,
		},
		computed: {
			...mapGetters('collections', ['slidesContent']),
			...mapGetters('slideshow', {'currentPresentableSlides': 'slides',}),
			...mapGetters('slideshow', ['presentableSortedSlidesIds', 'getSlidePositionById']),
			slides() {
				return this.slidesContent.map((slide) => ({
					header: slide.snippet.header,
					snippet: slide.snippet.content,
					media: slide.snippet.media !== null ? mediaMap[slide.snippet.media] : null,
					content: slide.content,
					id: slide.id
				}))
			},
			sortedSlides() {
				return this.currentSlideshowSlides.sort(({id: id1}, {id: id2}) => {
					const slideOne = this.currentPresentableSlides[id1]
					const slideTwo = this.currentPresentableSlides[id2]

					return slideOne.order_number - slideTwo.order_number
				})
			},
			presentableLoaded() {
				return Object.keys(this.currentPresentableSlides).length > 0
			},
			currentSlideshowSlides() {
				return (this.presentableLoaded && this.slides.filter((slide) => this.currentPresentableSlides[slide.id])) || []
			},
			currentSlideOrderNumber() {
				if (this.mode === 'bookmarked') {
					return this.selectedSlideIndex
				}
				return this.getSlideOrderNumberFromIndex(this.selectedSlideIndex)
			},
		},
		methods: {
			...mapActions('collections', ['addSlideToCollection', 'removeSlideFromCollection']),
			...mapActions('slideshow', ['setSortedSlidesIds','setup']),
			...mapActions(['toggleOverlay']),
			showSlide(index) {
				if (this.selectedSlideIndex === index) {
					this.$refs.slideshow.goToSlide(this.currentSlideOrderNumber)
				}
				this.selectedSlideIndex = index
			},
			onSlideBookmarked({slideId, hasReacted}) {
				if (hasReacted) {
					this.addSlideToCollection(slideId)
				} else {
					this.removeSlideFromCollection(slideId)
				}
			},
			mediaIcon(mediaType) {
				return this.mediaMap[mediaType].icon
			},
			getSlideOrderNumberFromIndex(index) {
				const selectedSlide = this.sortedSlides[index]

				return (selectedSlide
					&& this.currentPresentableSlides[selectedSlide.id]
					&& this.currentPresentableSlides[selectedSlide.id].order_number)
					|| 0
			},
			getSlideDisplayNumberFromIndex(index) {
				return this.getSlideOrderNumberFromIndex(index) + 1
			},
			toggleBookmarked() {
				if (this.mode === 'bookmarked') {
					this.showContent('full')
				} else {
					this.showContent('bookmarked')
				}
			},
			showContent(htmlContentKey) {
				if (htmlContentKey === 'bookmarked') {
					const slidesIds = this.currentSlideshowSlides.map(slide => slide.id)
					axios.post(getApiUrl(`slideshow_builder/.query`), {
						query: {
							whereIn: ['slides.id', slidesIds],
							where: [['presentables.presentable_type', 'App\\Models\\Category']],
						},
							join: [['presentables', 'slides.id', '=', 'presentables.slide_id']],
							order: {'presentables.order_number': 'asc'}
					}).then(({data}) => {
						this.loadedHtmlContents['bookmarked'] = data
						const sortedSlides = this.sortSlidesByOrderNumber(slidesIds)
						this.setSortedSlidesIds(sortedSlides)
						this.mode = htmlContentKey
						this.htmlContent = this.loadedHtmlContents[htmlContentKey];
					})
				} else {
					this.setSortedSlidesIds(this.presentableSortedSlidesIds)
					this.mode = htmlContentKey
					this.htmlContent = this.loadedHtmlContents[htmlContentKey];
				}
			},
			sortSlidesByOrderNumber(ids) {
				return ids.sort((id1, id2) => {
					const slideOne = this.currentPresentableSlides[id1]
					const slideTwo = this.currentPresentableSlides[id2]

					return slideOne.order_number - slideTwo.order_number
				})
			},
			loadSlideshow() {
				if (!this.savedSlidesCount) {
					return;
				}

				this.toggleOverlay({source: 'collection-slideshow', display: true})

				const fullSlideshowContentPromised = axios.get(getApiUrl(`slideshow_builder/category/${this.categoryId}`))

				this.setup({id: this.categoryId, type: this.presentableType})
					.then(() => this.showContent('bookmarked'))
					.then(() => this.toggleOverlay({source: 'collection-slideshow', display: false}))
					.catch(() => this.toggleOverlay({source: 'collection-slideshow', display: false}))

				fullSlideshowContentPromised.then(({data}) => this.loadedHtmlContents['full'] = data)
			}
		},
		watch: {
			'categoryId'() {
				this.htmlContent = ''
				this.loadedHtmlContents = {}
				this.selectedSlideIndex = 0

				this.loadSlideshow()
			}
		},
		mounted() {
			this.loadSlideshow()
		}
	}
</script>
