<template>
	<div class="slideshow-annotations" :class="{'is-mobile': isMobile}">
		<p class="metadata">Komentarze do slajdu {{currentSlideOrderNumber}}</p>
		<wnl-comments-list
		v-if="currentSlideId > 0"
		module="slideshow"
		urlParam="slide"
		commentableResource="slides"
		isUnique="true"
		:commentableId="currentSlideId"
		@commentsHidden="$emit('commentsHidden')"
		@commentsUpdated="onCommentsUpdated"
		:currentSlideId="currentSlideId"
		:canEditSlide="canEditSlide"
		></wnl-comments-list>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.slideshow-annotations
		flex: 1 auto
		margin: 0 $margin-base
		display: flex
		flex-direction: column

		&.is-mobile
			margin: 0 $margin-small

	.metadata
		margin-bottom: -$margin-base
</style>

<script>
	import {mapGetters} from 'vuex'

	import CommentsList from 'js/components/comments/CommentsList'

	export default {
		name: 'Annotations',
		components: {
			'wnl-comments-list': CommentsList,
		},
		props: {
			slideshowId: Number,
			currentSlideId: Number,
			canEditSlide: Boolean,
		},
		computed: {
			...mapGetters(['isMobile']),
			...mapGetters('slideshow', ['getSlidePositionById']),
			currentSlideOrderNumber() {
				return this.getSlidePositionById(this.currentSlideId) + 1
			}
		},
		methods: {
			onCommentsUpdated(comments) {
				this.$emit('annotationsUpdated', comments)
			},
		}
	}
</script>
