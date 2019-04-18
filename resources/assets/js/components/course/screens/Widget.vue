<template>
	<div class="wnl-screen-html content" v-html="content" />
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

	.wnl-screen-html
		margin: $margin-big 0

	.columns
		margin-bottom: 20px
		padding-bottom: 20px

	.image-container

		img
			display: block
			margin: 0 auto
			max-width: 200px
			max-height: 200px
</style>

<script>
export default {
	name: 'Widget',
	props: ['screenData'],
	computed: {
		content() {
			return this.screenData.content;
		}
	}
};
</script>
