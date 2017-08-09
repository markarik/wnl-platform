<template lang="html">
	<div class="wnl-slides-search" :class="{'is-mobile': isMobile}">
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

		&.is-mobile
			justify-content: center
</style>

<script>
	import {mapGetters} from 'vuex'

	import SlideThumbnail from 'js/components/global/search/SlideThumbnail'
	import {getApiUrl} from 'js/utils/env'

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
		computed: {
			...mapGetters(['isMobile']),
		},
		methods: {
			search() {
				if (!this.phrase) return

				axios.get(getApiUrl(`slides/.search?q=${this.phrase}`))
					.then(response => {
						this.hits = response.data.hits.hits
						this.$emit('searchComplete')
					})
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
