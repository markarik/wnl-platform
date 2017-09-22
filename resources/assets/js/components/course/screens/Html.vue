<template>

	<div class="wnl-screen-html" :class="{'wnl-repetitions': isRepetitions}">
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
	@import 'public/css/imageviewer'

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
	import {imageviewer} from 'vendor/imageviewer/imageviewer'
	imageviewer($, window, document)

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
				worked: false
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
				function setImageViewer() {
						ImageViewer().show(this.src);
				}

				let img = document.querySelectorAll(".wnl-screen-html img")

				if (img.length) {
					this.worked = true
				}
				img.forEach(function(e) {
					e.addEventListener('click', setImageViewer);
				})
			},
			onClick(event) {
				if (!this.worked) {
					document.querySelectorAll(".wnl-screen-html img").forEach(function(e) {
						e.addEventListener('click', setImageViewer);
					})

					if (event.target.tagName === 'IMG') {
						ImageViewer().show(event.target.src);
						this.worked = true
					}

				}
			}
		},
		beforeDestroy() {
			document.removeEventListener('click', setImageViewer);
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
