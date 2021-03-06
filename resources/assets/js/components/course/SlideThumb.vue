<template>
	<div
		:class="{
			'slide-thumb': true,
			'clickable': hasSlideClickListener
		}"
		@click="onClick"
	>
		<div class="thumb-meta">
			<span class="thumb-top-left"><slot /></span>
			<span v-if="media" class="icon is-tiny"><i class="fa" :class="media.icon" /></span>
		</div>
		<p class="thumb-heading metadata">{{slide.snippet.header}}</p>
		<div class="slide-snippet" v-html="slide.snippet.content" />
		<div v-if="media" class="slide-snippet has-media">
			<span class="icon is-tiny">
				<i class="fa" :class="media.icon" />
			</span>
			{{media.text}}
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

	$thumb-height: 90px
	$thumb-width: 160px

	.slide-thumb
		background-color: $color-white
		flex: 1 0 $thumb-width
		height: $thumb-height
		margin: $margin-small $margin-small $margin-base
		max-width: $thumb-width
		overflow: hidden
		padding: $margin-small
		position: relative
		text-align: center
		transition: color $transition-length-base
		width: $thumb-width

		&:hover
			color: $color-ocean-blue

			&::after
				height: 0

		.thumb-meta
			align-items: center
			display: flex
			justify-content: space-between

		.thumb-top-left
			color: $color-lighter-gray
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

		&::after
			+white-shadow-inside()

			bottom: 0
			content: ''
			display: block
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
};

export default {
	props: {
		slide: {
			type: Object,
			required: true,
		}
	},
	computed: {
		hasSlideClickListener() {
			return this.$listeners && this.$listeners.slideClick;
		},
		media() {
			return this.slide.snippet.media !== null ? mediaMap[this.slide.snippet.media] : null;
		}
	},
	methods: {
		onClick() {
			this.$emit('slideClick');
		}
	}
};
</script>
