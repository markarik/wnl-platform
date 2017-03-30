<template>
	<div class="wnl-screen-html">
		{{content}}
	</div>
</template>

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
