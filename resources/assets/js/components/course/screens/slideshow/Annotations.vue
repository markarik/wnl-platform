<template>
	<div>
		<p class="metadata">Komentarze do slajdu</p>
		<wnl-comments-list
			v-if="currentSlideId > 0"
			module="slideshow"
			commentableResource="slides"
			isUnique="true"
			:commentableId="currentSlideId"
			@commentsHidden="$emit('commentsHidden')"
			@commentsUpdated="onCommentsUpdated"
		></wnl-comments-list>
	</div>
</template>

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
			currentSlide: Number,
		},
		computed: {
			...mapGetters('slideshow', ['getSlideId']),
			currentSlideId() {
				return this.getSlideId(this.currentSlide - 1)
			},
		},
		methods: {
			onCommentsUpdated(comments) {
				this.$emit('annotationsUpdated', comments)
			},
		},
	}
</script>
