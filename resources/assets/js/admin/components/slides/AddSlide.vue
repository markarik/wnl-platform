<template>
	<wnl-slides-editor
		tile="Dodaj slajd"
	/>
</template>

<script>
	import SlidesEditor from 'js/admin/components/slides/SlidesEditor'

	export default {
		name: 'AddSlide',
		components: {
			'wnl-slides-editor': SlidesEditor,
		},
	}
</script>
