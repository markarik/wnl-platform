<template>
	<div
		class="wnl-screen-html"
		:class="{'wnl-repetitions': isRepetitions}"
		@click="onClick"
	>
		<div class="content" v-html="content" />
		<p v-if="showBacklink" class="end-button has-text-centered">
			<router-link :to="{name: 'dashboard'}" class="button is-primary is-outlined">
				Wróć do auli
			</router-link>
		</p>
		<div class="image-gallery-wrapper" :class="{ isVisible: isVisible }">
			<div class="image-container" />
			<span class="prev" @click="goToImage(previousImageIndex)"><i class="fa fa-angle-left" /></span>
			<span class="next" @click="goToImage(nextImageIndex)"><i class="fa fa-angle-right" /></span>
			<span class="iv-close" @click="isVisible = false" />
			<div class="footer-info">
				<span class="current">{{currentImageIndex + 1}}</span>/<span class="total">{{images.length}}</span>
			</div>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

	.wnl-screen-html .content img:hover
		opacity: 0.7
		cursor: pointer

	.wnl-screen-html
		&.wnl-repetitions
			.content
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

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-screen-html
		margin: $margin-big 0

		.image-gallery-wrapper
			background-color: black
			display: none
			height: 100%
			left: 0
			position: fixed
			right: 0
			top: 0
			width: 100%
			z-index: $z-index-fullscren
			&.isVisible
				display: block
			.image-container
				display: block
				height: 100%
				width: 100%
			.prev,.next
				text-align: center
				color: #929AA8
				display: inline-block
				font-size: $font-size-plus-8
				height: 20vh
				position: absolute
				top: calc(50% - 4.5rem)
				z-index: 101
			.next
				padding-right: 5%
				right: 0
				text-align: right
			.prev
				padding-left: 5%
			.footer-info
				bottom: 0
				color: white
				font-size: 24px
				height: 50px
				position: absolute
				text-shadow: 2px 2px #5F5A5A
				left: 0
				line-height: 50px
				text-align: center
				width: 100%

		.content
			font-size: $font-size-plus-1
			line-height: $line-height-plus
</style>

<script>
import _ from 'lodash';
import { imageviewer } from 'vendor/imageviewer/imageviewer';
import { nextTick } from 'vue';
import $ from 'jquery';

imageviewer($, window, document);
function showImage(src) {
	window.ImageViewer($('.image-gallery-wrapper .image-container'), { snapViewPersist: false }).load(src);
}


export default {
	name: 'Html',
	props: ['screenData', 'showBacklink'],
	data() {
		return {
			imagesLoaded: false,
			imagesSelector: '.wnl-screen-html img',
			isVisible: false,
			images: [],
			currentImageIndex: -1,
		};
	},
	computed: {
		content() {
			return this.screenData.content;
		},
		isRepetitions() {
			return this.screenData.name.indexOf('Powtórki') > -1;
		},
		previousImageIndex() {
			return this.currentImageIndex > 0 ? this.currentImageIndex - 1 : this.images.length -1;
		},
		nextImageIndex() {
			return this.currentImageIndex === this.images.length -1 ? 0 : this.currentImageIndex + 1;
		},
	},
	methods: {
		goToImage(index) {
			if (index < 0 || !this.images.length) return;

			nextTick(() => {
				const image = this.images[index];
				const idx = image ? index : 0;
				showImage(this.images[idx].src);
			});

			this.currentImageIndex = index;
		},
		wrapEmbedded() {
			let iframes = this.$el.getElementsByClassName('ql-video'),
				wrapperClass = 'ratio-16-9-wrapper';

			if (iframes.length > 0) {
				_.each(iframes, (iframe) => {
					let wrapper = document.createElement('div'),
						parent = iframe.parentNode;

					wrapper.className = wrapperClass;
					parent.replaceChild(wrapper, iframe);
					wrapper.appendChild(iframe);
				});
			}
		},
		addFullscreen() {
			this.images = document.querySelectorAll(this.imagesSelector);

			if (this.images.length) {
				this.imagesLoaded = true;
			}

			this.images.forEach((image, index) => {
				image.addEventListener('click', () => {
					this.isVisible = true;
					this.goToImage(index);
				});
			});
		},
		onClick(event) {
			if (!this.imagesLoaded) {
				this.images = document.querySelectorAll(this.imagesSelector);
				this.images.forEach((image, index) => {
					image.addEventListener('click', () => {
						this.isVisible = true;
						this.goToImage(index);
					});
				});

				if (event.target.matches(this.imagesSelector)) {
					const index = this.images.findIndex((image) => image.src === event.target.src);
					this.goToImage(index);
					this.imagesLoaded = true;
					this.isVisible = true;
				}
			}
		},
		onKeydown(e) {
			switch(e.keyCode) {
			case 37: // left arrow
				this.goToImage(this.previousImageIndex);
				break;
			case 39: // right arrow
				this.goToImage(this.nextImageIndex);
				break;
			case 27: // esc
				this.isVisible = false;
				break;
			}
		}
	},
	mounted() {
		this.wrapEmbedded();
		this.addFullscreen();
		document.body.addEventListener('keydown', this.onKeydown);
	},
	watch: {
		screenData() {
			nextTick(() => this.wrapEmbedded());
		}
	}

};
</script>
