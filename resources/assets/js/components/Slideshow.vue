<template>
	<div class="wnl-snippet-slideshow">
		<iframe :src="slideshowUrl" width="100%" height="400"></iframe>
	</div>
</template>
<style>

</style>
<script>
	export default {
		name: 'Slideshow',
		props: ['screenData'],
		computed: {
			snippetId() {
				return this.screenData.id
			},
			slideshowUrl() {
				return $fn.getBaseUrl('slideshow-builder/' + this.snippetId)
			}
		}
	}
</script>
