<template lang="html">
	<div class="wnl-slides-search">
		<div class="slide-thumb" :key="index" v-for="(slide, index) in slides" @click="emitResultClicked(slide)">
			<div class="thumb-meta">
				<span class="icon is-tiny" v-if="slide.media"><i class="fa" :class="slide.media.icon"></i></span>
			</div>
			<p class="thumb-heading metadata" v-html="slide.header"></p>
			<div class="slide-snippet" v-html="slide.snippet"></div>
			<div class="slide-snippet has-media" v-if="slide.media">
				<span class="icon is-tiny">
					<i class="fa" :class="slide.media.icon"></i>
				</span>
				{{ slide.media.text }}
			</div>
			<div class="shadow"></div>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'
	$thumb-height: 190px
	$thumb-width: 250px

	.wnl-slides-search
		justify-content: space-around
		display: flex
		flex-wrap: wrap
		margin: $margin-base 0

	.slide-thumb
		cursor: pointer
		flex: 1 0 $thumb-width
		height: $thumb-height
		margin: $margin-small $margin-small $margin-base
		max-width: $thumb-width
		padding: $margin-small
		text-align: center
		transition: color $transition-length-base
		overflow-y: hidden

		em
			color: $color-ocean-blue
			font-weight: 700

		&:hover
			color: $color-ocean-blue
			transition: color $transition-length-base

			.shadow
				height: 0
				transition: height $transition-length-base

		.thumb-meta
			align-items: center
			display: flex
			justify-content: space-between

		.thumb-slide-number
			font-size: $font-size-minus-3
			line-height: $line-height-minus
			margin-bottom: $margin-tiny
			text-align: left

		.thumb-heading
			line-height: $line-height-minus
			margin-bottom: $margin-small

		.slide-snippet
			font-size: $font-size-minus-2
			line-height: $line-height-minus

			&.has-media
				margin-top: $margin-small

		.shadow
			+white-shadow-inside()

			bottom: 0
			height: 50%
			position: absolute
			transition: height $transition-length-base
			width: 100%
</style>

<script>
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
		props: ['phrase'],
		computed: {
			slides() {
				return this.hits.map((hit) => ({
					header: this.getHighlight(hit, 'snippet.header') || hit._source.snippet.header,
					snippet: this.getHighlight(hit, 'snippet.content') || hit._source.snippet.content,
					context: hit._source.context,
					media: hit._source.snippet.media !== null ? mediaMap[hit._source.snippet.media] : null,
					content: hit._source.content,
					id: hit._source.id
				}))
			},
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
						.then(response => {
							this.hits = response.data.hits.hits
						})
			},
			getHighlight(hit, key) {
				const highlight = _.get(hit, `highlight["${key}"]`)

				if (Array.isArray(highlight)) {
					return highlight.join('...')
				}

				return highlight
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
