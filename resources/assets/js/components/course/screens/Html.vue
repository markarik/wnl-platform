<template>
	<div class="wnl-screen-html" :class="{'wnl-repetitions': isRepetitions}" @click="onClick">
		<div class="content" v-html="content">
		</div>
		<p class="end-button has-text-centered" v-if="showBacklink">
			<router-link :to="{name: 'dashboard'}" class="button is-primary is-outlined">
				Wróć do auli
			</router-link>
		</p>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

	.wnl-screen-html
		margin: $margin-big 0
		img:hover
			opacity: 0.7


	.wnl-repetitions
		ol
			counter-reset: li
			margin-left: 0
			padding-left: $margin-base

		ol li
			list-style-type: none

			&::before
				color: $color-ocean-blue
				content: counter(li) ". "
				counter-increment: li
				font-weight: $font-weight-bold

		ol li:nth-child(5n)
			margin-bottom: $margin-big

		ol li:nth-child(10n-4),
		ol li:nth-child(10n-3),
		ol li:nth-child(10n-2),
		ol li:nth-child(10n-1),
		ol li:nth-child(10n)

			&::before
				color: $color-purple
</style>

<script>
	import _ from 'lodash'

	function showImage() {
		// this is not Vue component, it's "event triggered" this
		ImageViewer({snapViewPersist: false}).show(this.src);
	}


	export default {
		name: 'Html',
		props: ['screenData', 'showBacklink'],
		computed: {
			content() {
				return this.screenData.content
			},
			isRepetitions() {
				return this.screenData.name.indexOf('Powtórki') > -1
			}
		},
		data() {
			return {
				imagesLoaded: false,
				imagesSelector: '.wnl-screen-html img',
			}
		},
		methods: {
			wrapEmbedded() {
				let iframes = this.$el.getElementsByClassName('ql-video'),
					wrapperClass = 'ratio-16-9-wrapper'

				if (iframes.length > 0) {
					_.each(iframes, (iframe) => {
						let wrapper = document.createElement('div'),
							parent = iframe.parentNode

						wrapper.className = wrapperClass
						parent.replaceChild(wrapper, iframe)
						wrapper.appendChild(iframe)
					})
				}
			},
			addFullscreen() {
				const images = document.querySelectorAll(this.imagesSelector)

				if (images.length) {
					this.imagesLoaded = true
				}

				images.forEach((image) => {
					image.addEventListener('click', showImage);
				})
			},
			onClick(event) {
				if (!this.imagesLoaded) {
					document.querySelectorAll(this.imagesSelector).forEach((image) => {
						image.addEventListener('click', showImage);
					})

					if (event.target.matches(this.imagesSelector)) {
						ImageViewer().show(event.target.src);
						this.imagesLoaded = true
					}
				}
			}
		},
		beforeDestroy() {
			document.removeEventListener('click', showImage);
		},
		mounted() {
			this.wrapEmbedded();
			this.addFullscreen();
		},
		updated() {
			this.wrapEmbedded()
		},
	}
</script>
