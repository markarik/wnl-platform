<template>
	<div class="wnl-snippet wnl-ratio-16-9">
		<div class="wnl-slideshow-container" id="wnl-slideshow"></div>
	</div>
</template>
<style lang="sass">
	.wnl-ratio-16-9
		padding-bottom: 56.25%
		position: relative
		width: 100%

	.wnl-slideshow-container
		bottom: 0
		left: 0
		position: absolute
		right: 0
		top: 0

		iframe
			height: 100%
			width: 100%
</style>
<script>
	import Postmate from 'postmate'

	export default {
		name: 'Slideshow',
		data() {
			return {
				child: {},
				currentSlide: 1,
				loaded: false
			}
		},
		props: ['screenData'],
		computed: {
			container() {
				return document.getElementById('wnl-slideshow')
			},
			snippetId() {
				return this.screenData.id
			},
			slideshowUrl() {
				return $fn.getUrl('slideshow-builder/' + this.snippetId)
			},
		},
		mounted() {
			Postmate.debug = global.$fn.isDevEnv()
			const handshake = new Postmate({
				container: this.container,
				url: this.slideshowUrl
			});
			handshake.then(child => {
				child.on('loaded', (status) => {
					if (status) {
						this.child = child
					}
				});
			});
		}
	}
</script>
