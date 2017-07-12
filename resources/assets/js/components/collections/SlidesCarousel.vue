<template>
	<div class="wnl-slides-collection">
		<p class="title is-4">Twoja kolekcja slajdów</p>
		<wnl-slideshow
			:screenData="screenData"
			:presentableId="categoryId"
			:presentableType="presentableType"
			:preserveRoute="true"
			:slideOrderNumber="currentSlide.order_number"
		></wnl-slideshow>
		<carousel class="wnl-carousel" :paginationEnabled="false" :navigationEnabled="true" :perPage="4">
			<slide class="wnl-slide" v-bind:key="index" v-for="(slide, index) in slides">
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
				// TODO this has to be smarter - show the first slide from collection
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
			...mapGetters('slideshow', {'allSlides': 'slides'}),
			slides() {
				return this.slidesContent.map((slide) => ({
					header: slide.snippet.header,
					snippet: slide.snippet.content,
					media: slide.snippet.media,
					content: slide.content,
					id: slide.id
				}))
			},
			currentSlide() {
				const selectedSlide = this.slides[this.selectedSlideIndex];

				return selectedSlide && this.allSlides[selectedSlide.id] || {}
			}
		},
		methods: {
			showSlide(index) {
				this.selectedSlideIndex = index;
			}
		}
	}
</script>
