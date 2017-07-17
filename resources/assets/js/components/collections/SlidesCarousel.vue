<template>
	<div class="wnl-slides-collection">
		<p class="title is-4">Twoja kolekcja slajdów</p>
		<wnl-slideshow
			:screenData="screenData"
			:presentableId="categoryId"
			:presentableType="presentableType"
			:preserveRoute="true"
			:slideOrderNumber="currentSlideOrderNumber"
			@slideBookmarked="onSlideBookmarked"
		></wnl-slideshow>

		<div v-if="presentableLoaded && sortedSlides.length" class="slides-carousel">
			<div class="slide-thumb" :key="index" v-for="(slide, index) in sortedSlides" @click="showSlide(index)">
				<p class="metadata">{{slide.header}}</p>
				<div class="slide-snippet" v-if="!slide.media" v-html="slide.snippet"></div>
				<div class="slide-snippet" v-else>Obraz lub film</div>
			</div>
		</div>
	</div>
</template>
<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-slides-collection
		margin-top: $margin-base

	.wnl-carousel
		justify-content: space-around
		margin: $margin-base 0

	.VueCarousel-inner
		margin: 0 auto

	.slide
		width: 100%
		height: 400px

	.slides-carousel
		display: flex
		flex-wrap: nowrap
		overflow-y: hidden
		overflow-x: auto

	.slide-thumb
		background-color: white
		border: $border-light-gray
		cursor: pointer
		flex: 1 0 160px
		height: 90px
		margin-bottom: $margin-base
		margin-right: $margin-small
		overflow: hidden
		padding: $margin-small
		width: 160px
</style>

<script>
	import { mapActions, mapGetters } from 'vuex'
	import Slideshow from 'js/components/course/screens/slideshow/Slideshow.vue'

	export default {
		name: 'SlidesCarousel',
		props: ['categoryId'],
		data() {
			return {
				selectedSlideIndex: 0,
				screenData: {
					type: 'slideshow'
				},
				presentableType: 'App\\Models\\Category',
			}
		},
		components: {
			'wnl-slideshow': Slideshow,
		},
		computed: {
			...mapGetters('collections', ['slidesContent']),
			...mapGetters('slideshow', {'currentPresentableSlides': 'slides'}),
			slides() {
				return this.slidesContent.map((slide) => ({
					header: slide.snippet.header,
					snippet: slide.snippet.content,
					media: slide.snippet.media,
					content: slide.content,
					id: slide.id
				}))
			},
			sortedSlides() {
				const filteredSlides = [...this.currentSlideshowSlides]

				filteredSlides.sort(({id: id1}, {id: id2}) => {
					const slideOne = this.currentPresentableSlides[id1]
					const slideTwo = this.currentPresentableSlides[id2]

					return slideOne.order_number - slideTwo.order_number
				})

				return filteredSlides
			},
			presentableLoaded() {
				return Object.keys(this.currentPresentableSlides).length > 0
			},
			currentSlideshowSlides() {
				return (this.presentableLoaded && this.slides.filter((slide) => this.currentPresentableSlides[slide.id])) || []
			},
			currentSlideOrderNumber() {
				const selectedSlide = this.sortedSlides[this.selectedSlideIndex];

				return selectedSlide && this.currentPresentableSlides[selectedSlide.id].order_number || 0
			}
		},
		methods: {
			...mapActions('collections', ['addSlideToCollection', 'removeSlideFromCollection']),
			showSlide(index) {
				this.selectedSlideIndex = index;
			},
			onSlideBookmarked({slideId, hasReacted}) {
				if (hasReacted) {
					this.addSlideToCollection(slideId)
				} else {
					this.removeSlideFromCollection(slideId)
				}
			}
		},
		watch: {
			'categoryId'() {
				this.selectedSlideIndex = 0
			}
		}
	}
</script>
