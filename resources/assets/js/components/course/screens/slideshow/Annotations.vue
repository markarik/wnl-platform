<template>
	<div>
		<p class="metadata">Komentarze do slajdu</p>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>

</style>

<script>
	import {mapActions} from 'vuex'

	export default {
		name: 'Annotations',
		props: {
			slideshowId: Number,
		},
		methods: {
			...mapActions('slideshow', ['setup'])
		},
		mounted() {
			this.setup(this.slideshowId)
		}
	}
</script>
