<template>
	<div class="wnl-screen-html content" v-html="content"></div>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

	.wnl-screen-html
		margin: $margin-big 0
</style>

<script>
	export default {
		name: 'Html',
		props: ['screenData'],
		computed: {
			content() {
				return this.screenData.content
			}
		}
	}
</script>
