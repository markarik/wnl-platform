<template>
	<div class="field">
		<div class="control screens-control">
			<div v-for="slide in slides" :context="slide.context" :key="slide.id">
				{{slide.id}}
				<i class="fa fa-times" @click="removeSlide(slide)"></i>
			</div>
			<input
				v-model="screenIdInput"
				class="input"
				type="text"
				placeholder="Id screena"
				ref="slideIdInput"
			>
			<input
				v-model="slideNumberInput"
				class="input"
				type="number"
				placeholder="Numer porządkowy"
				ref="orderNumberInput"
			>
			<a
				@click="onButtonClicked"
			>
				Dodaj
			</a>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

</style>

<script>
	import { formInput } from 'js/mixins/form-input'
	import SlideLink from 'js/components/global/SlideLink'
	import _ from 'lodash'
	import { mapActions } from 'vuex'

	const keys = {
		enter: 13,
		esc: 27,
		arrowUp: 38,
		arrowDown: 40
	}

	export default {
		name: 'SlideIds',
		components: {
			'wnl-slide-link': SlideLink
		},
		props: ['defaultSlides'],
		data: function () {
			return {
				autocompleteItems: [],
				slides: [],
				screenIdInput: '',
				slideNumberInput: '',
			}
		},
		computed: {
			default() {
				return ''
			}
		},
		methods: {
			...mapActions(['getSlideDataForQuizEditor']),
			onButtonClicked() {
				this.getSlideDataForQuizEditor({
					screenId: this.screenIdInput,
					slideNumber: this.slideNumberInput
				}).then(data => {
					if (this.slides.map(slide => slide.id).indexOf(data.id) === -1) {
						this.slides.push(data);
					}
				})
			},

			removeSlide(slide) {
				this.slides = _.filter(
					this.tags,
					foundSlide => slide.id !== foundSlide.id
				)
			},

			haveSlidesChanged() {
				if (this.slides.length !== this.defaultSlides.length) return true

				return !!this.slides.some(slide => !_.find(this.defaultSlides, defSlide => defSlide.id === slide.id))
			}
		},
		watch: {
			defaultSlides() {
				this.tags = this.defaultSlides.slice()
			}
		}
	}
</script>
