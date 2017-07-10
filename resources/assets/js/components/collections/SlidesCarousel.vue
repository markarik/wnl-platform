<template>
	<div>
		<h3>Twoja kolekcja slajdów</h3>
		<wnl-slideshow
			:screenData="screenData"
			:presentableId="categoryId"
			:presentableType="presentableType"
			:preserveRoute="true"
		></wnl-slideshow>
		<carousel class="wnl-carousel" :navigationEnabled="true" :perPage="4">
			<slide class="wnl-slide" v-bind:key="index" v-for="(slide, index) in slides">
				<div class="slide-thumb" @click="showSlide(index)">
					<h4>{{slide.header}}</h4>
					<div class="slide-snippet" v-if="!slide.media" v-html="slide.snippet"></div>
					<div class="slide-snippet" v-else>Obrazki</div>
				</div>
			</slide>
		</carousel>
	</div>
</template>
<style lang="sass" rel="stylesheet/sass" scoped>
	.wnl-carousel
		justify-content: space-around;
		margin-bottom: 10px;

	.VueCarousel-inner
		margin: 0 auto;
	.slide
		width: 100%;
		height: 400px;
		background-color: grey;

	.slide-thumb
		height: 100px;
		background-color: lime;
		border: 1px solid grey;
		cursor: pointer;
</style>

<script>
	import {mapActions, mapGetters} from 'vuex'
	import { Carousel, Slide } from 'vue-carousel';
	import Slideshow from 'js/components/course/screens/slideshow/Slideshow.vue'

	export default {
		name: 'SlidesCarousel',
		components: {
			Carousel,
			Slide
		},
		props: ['categoryId'],
		data() {
			return {
				selectedSlide: 0,
				screenData: {
					type: 'slideshow'
				},
				presentableType: 'App\\Models\\Category'
			}
		},
		components: {
			'wnl-slideshow': Slideshow,
			'carousel': Carousel
		},
		computed: {
			...mapGetters('collections', ['slidesContent']),
			slides() {
				return this.slidesContent.map((slide) => ({
					header: slide.snippet.header,
					snippet: slide.snippet.content,
					media: slide.snippet.media,
					content: slide.content
				}))
			},
			currentSlide() {
				return this.slides[this.selectedSlide] && this.slides[this.selectedSlide].content
			}
		},
		methods: {
			showSlide(index) {
				this.selectedSlide = index;
			}
		}
	}
</script>
