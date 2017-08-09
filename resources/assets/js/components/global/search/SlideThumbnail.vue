<template>
	<router-link :to="to">
		<div class="slide-thumb">
			<div class="thumb-meta">
				<span class="icon is-tiny" v-if="media"><i class="fa" :class="media.icon"></i></span>
			</div>
			<p class="thumb-heading metadata" v-html="header"></p>
			<div class="slide-snippet" v-html="snippet"></div>
			<div class="slide-snippet has-media" v-if="media">
				<span class="icon is-tiny">
					<i class="fa" :class="media.icon"></i>
				</span>
				{{ media.text }}
			</div>
		</div>
	</router-link>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'
	$thumb-height: 190px
	$thumb-width: 250px

	.slide-thumb
		border: $border-light-gray
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
			color: $color-blue
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
		name: 'SlideThumbnail',
		props: {
			hit: {
				required: true,
				type: Object,
			},
		},
		computed: {
			context() {
				return this.hit._source.context
			},
			content() {
				return this.hit._source.content
			},
			header() {
				return this.getHighlight(this.hit, 'snippet.header') || this.hit._source.snippet.header
			},
			id() {
				return this.hit._source.id
			},
			media() {
				return this.hit._source.snippet && this.hit._source.snippet.media !== null ? mediaMap[this.hit._source.snippet.media] : null
			},
			snippet() {
				return this.getHighlight(this.hit, 'snippet.content') || this.hit._source.snippet.content
			},
			to() {
				return {
					name: 'screens',
					params: {
						courseId: this.context.course.id,
						lessonId: this.context.lesson.id,
						screenId: this.context.screen.id,
						slide: this.context.orderNumber + 1,
					}
				}
			},
		},
		methods: {
			getHighlight(hit, key) {
				const highlight = _.get(hit, `highlight["${key}"]`)

				if (Array.isArray(highlight)) {
					return highlight.join('...')
				}

				return highlight
			},
		}
	}
</script>
