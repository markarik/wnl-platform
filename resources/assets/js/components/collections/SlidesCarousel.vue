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
		<carousel class="wnl-carousel" :paginationEnabled="false" :navigationEnabled="true" :perPage="4" v-if="presentableLoaded && sortedSlides.length">
			<slide class="wnl-slide" v-bind:key="index" v-for="(slide, index) in sortedSlides">
				<div class="slide-thumb" @click="showSlide(index)">
					<p class="metadata">{{slide.header}}</p>
					<div class="slide-snippet" v-if="!slide.media" v-html="slide.snippet"></div>
					<div class="slide-snippet" v-else>Obraz lub film</div>
				</div>
			</slide>
		</carousel>
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

	.slide-thumb
		background-color: white
		border: $border-light-gray
		cursor: pointer
		height: 100px
		margin-right: $margin-small
		padding: $margin-small
</style>

<script>
	import { mapActions, mapGetters } from 'vuex'
	import { Carousel, Slide } from 'vue-carousel';
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
			Carousel,
			Slide
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
