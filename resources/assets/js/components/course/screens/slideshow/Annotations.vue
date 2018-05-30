<template>
	<div class="slideshow-annotations" :class="{'is-mobile': isMobile}">
		<div
			class="slideshow-annotations__header"
			:class="{canEdit: 'can-edit'}"
			v-if="canEdit">
			<p class="metadata">Komentarze do slajdu {{currentSlideOrderNumber}}</p>
			<wnl-edit-slide-button/>
		</div>
		<p v-else class="metadata">Komentarze do slajdu {{currentSlideOrderNumber}}</p>
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
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.slideshow-annotations
		flex: 1 auto
		margin: 0 $margin-base

		.slideshow-annotations__header
			display: flex
			flex-direction: row
			justify-content: space-between

		&.is-mobile
			margin: 0 $margin-small

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
			...mapGetters(['isMobile', 'isAdmin', 'isModerator']),
			...mapGetters('slideshow', ['getSlidePositionById']),
			canEdit() {
				return this.isModerator || this.isAdmin
			},
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
