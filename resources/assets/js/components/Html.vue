<template>
	<div class="wnl-snippet-html">
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
