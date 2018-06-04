<template>
	<div class="slideshow-annotations" :class="{'is-mobile': isMobile, 'can-edit': isAdmin}">
		<wnl-edit-slide-button
			:currentSlideId="currentSlideId"
			class="slideshow-annotations__edit-button"
			v-if="isAdmin"/>
		<div class="slideshow-annotations__comments">
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
			></wnl-comments-list>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.slideshow-annotations
		flex: 1 auto
		margin: 0 $margin-base
		display: flex

		&.is-mobile
			margin: 0 $margin-small

		&.can-edit
			flex-direction: row-reverse
			justify-content: space-between

	.metadata
		margin-bottom: -$margin-base
</style>

<script>
	import {mapGetters} from 'vuex'

	import EditSlideButton from 'js/admin/components/slides/EditSlideButton'
	import CommentsList from 'js/components/comments/CommentsList'

	export default {
		name: 'Annotations',
		components: {
			'wnl-comments-list': CommentsList,
			'wnl-edit-slide-button': EditSlideButton,
		},
		props: {
			slideshowId: Number,
			currentSlideId: Number,
		},
		computed: {
			...mapGetters(['isMobile', 'isAdmin']),
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
