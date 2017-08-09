<template lang="html">
	<div class="wnl-slides-search">
		<wnl-slide-thumbnail
			v-for="(hit, index) in hits"
			:key="index"
			:hit="hit"
		/>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-slides-search
		justify-content: flex-start
		display: flex
		flex-wrap: wrap
		margin: $margin-base 0
</style>

<script>
	import SlideThumbnail from 'js/components/global/search/SlideThumbnail'
	import {getApiUrl} from 'js/utils/env'

	const mediaMap = {
		chart: {
			icon: 'fa-sitemap',
			text: 'Diagram',
		},
		movie: {
			icon: 'fa-film',
			text: 'Film',
		},
		audio: {
			icon: 'fa-music',
			text: 'Nagranie audio',
		},
	}

	export default {
		name: 'SlidesSearch',
		components: {
			'wnl-slide-thumbnail': SlideThumbnail,
		},
		props: {
			phrase: {
				required: true,
				type: String,
			}
		},
		data() {
			return {
				hits: [],
			}
		},
		methods: {
			search() {
				if (!this.phrase) return

				axios.get(getApiUrl(`slides/.search?q=${this.phrase}`))
					.then(response => this.hits = response.data.hits.hits)
			},
			emitResultClicked(result) {
				this.$emit('resultClicked', result)
			}
		},
		watch: {
			'phrase'() {
				this.search()
			}
		}
	}
</script>
