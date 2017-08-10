<template>

	<div class="wnl-screen-html" :class="{'wnl-repetitions': isRepetitions}">
		<fullscreen :imagesSources="imagesSources" :currentImageSource="currentImageSource" v-if="currentImageSource"
		></fullscreen>
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
	import Fullscreen from 'js/components/global/Fullscreen'

	export default {
		name: 'Html',
		data() {
			return {
				imagesSources: [],
				currentImageSource: ''
			}
		},
		props: ['screenData', 'showBacklink'],
		components: {
			'fullscreen': Fullscreen
		},
		computed: {
			content() {
				return this.screenData.content
			},
			isRepetitions() {
				return this.screenData.name.indexOf('Powtórki') > -1
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
			clickOnImg(event) {
				this.currentImageSource = event.target.src
			},
		},
		mounted() {
			this.wrapEmbedded();
			var img = document.querySelectorAll(".wnl-screen-html img");
			var i;
			for (i = 0; i < img.length; i++) {
				this.imagesSources.push(img[i].src);
				img[i].addEventListener('click', this.clickOnImg)
			};
		},
		updated() {
			this.wrapEmbedded()
		},
	}
</script>
