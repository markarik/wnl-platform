<template lang="html">
	<svg><use :xlink:href="href"/></svg>
</template>

<script>
	export default {
		props: ['name'],
		computed: {
			href() {
				return global.$fn.getUrl('images/icons/sprite.svg#' + this.name)
			}
		}
	}
</script>
