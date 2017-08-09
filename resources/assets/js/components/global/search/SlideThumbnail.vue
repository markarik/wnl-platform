<template>
	<router-link class="slide-router-link unselectable" :to="to">
		<div class="slide-context">
			<div class="group-and-lesson">
				<span class="group-name" v-text="groupName"></span>
				<span class="icon is-small"><i class="fa fa-angle-right"></i></span>
				<span class="lesson-name" v-text="lessonName"></span>
			</div>
			<div class="section-name">
				{{sectionName}}
			</div>
		</div>
		<div class="slide-thumb">
			<div class="thumb-meta">
				<span class="slide-number">{{slideNumber}}</span>
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

	.slide-router-link
		color: $color-gray
		margin: $margin-small $margin-small $margin-base

	.slide-context
		margin-bottom: $margin-small

		.group-and-lesson
			align-items: center
			display: flex
			font-size: $font-size-minus-2
			justify-content: center

			.group-name
				letter-spacing: 1px
				text-transform: uppercase

		.section-name
			font-size: $font-size-minus-2
			font-weight: $font-weight-bold
			line-height: $line-height-minus
			text-align: center

	.slide-thumb
		border: $border-light-gray
		cursor: pointer
		flex: 1 0 $thumb-width
		height: $thumb-height
		max-width: $thumb-width
		overflow-y: auto
		padding: $margin-small
		text-align: center
		transition: color $transition-length-base
		width: $thumb-width

		&:hover
			color: $color-ocean-blue
			transition: color $transition-length-base

		.thumb-meta
			align-items: center
			display: flex
			justify-content: space-between

			.slide-number
				font-size: $font-size-minus-3
				line-height: $line-height-minus
				margin-bottom: $margin-tiny

		.thumb-heading
			line-height: $line-height-minus
			margin-bottom: $margin-small

		.slide-snippet
			font-size: $font-size-minus-2
			line-height: $line-height-minus

			&.has-media
				margin-top: $margin-small
</style>

<script>
	import {truncate} from 'lodash'

	import {mapGetters} from 'vuex'

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
			...mapGetters('course', ['getGroup', 'getLesson', 'getSection']),
			context() {
				return this.hit._source.context
			},
			content() {
				return this.hit._source.content
			},
			groupName() {
				return truncate(this.getGroup(this.context.group.id).name, {length: 15})
			},
			header() {
				return this.getHighlight(this.hit, 'snippet.header') || this.hit._source.snippet.header
			},
			id() {
				return this.hit._source.id
			},
			lessonName() {
				return truncate(this.getLesson(this.context.lesson.id).name, {length: 20})
			},
			media() {
				return this.hit._source.snippet && this.hit._source.snippet.media !== null ? mediaMap[this.hit._source.snippet.media] : null
			},
			sectionName() {
				return truncate(this.getSection(this.context.section.id).name, {length: 40})
			},
			slideNumber() {
				return this.context.orderNumber + 1
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
						slide: this.slideNumber,
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
