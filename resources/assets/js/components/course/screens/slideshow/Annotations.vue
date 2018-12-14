<template>
	<div class="slideshow-annotations" :class="{'is-mobile': isMobile}">
		<div>
			<p class="metadata">
				Komentarze do slajdu {{currentSlideOrderNumber}}
			</p>
			<div v-if="isLoadingComments" class="loading-comments">
				<span class="icon is-small status-icon">
					<i class="fa fa-circle-o-notch fa-spin"></i>
				</span>
				Ładuję komentarze...
			</div>
		</div>
		<wnl-comments-list
			v-if="currentSlideId > 0 && !isLoadingComments"
			module="slideshow"
			urlParam="slide"
			commentableResource="slides"
			isUnique="true"
			:commentableId="currentSlideId"
			:currentSlideId="currentSlideId"
			@commentsHidden="$emit('commentsHidden')"
			@commentsUpdated="onCommentsUpdated"
		>
		<wnl-edit-slide-button
			:currentSlideId="Number(currentSlideId)"
			:screenId="Number(screenId)"
			v-if="isAdmin"
		/>
	</wnl-comments-list>
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

	.loading-comments
		color: $color-gray-dimmed
		margin: $margin-base 0

	.metadata
		margin-bottom: -$margin-base
</style>

<script>
import {mapGetters} from 'vuex';

import EditSlideButton from 'js/admin/components/slides/EditSlideButton';
import CommentsList from 'js/components/comments/CommentsList';

export default {
	name: 'Annotations',
	components: {
		'wnl-comments-list': CommentsList,
		'wnl-edit-slide-button': EditSlideButton,
	},
	props: {
		slideshowId: Number,
		screenId: Number,
		currentSlideId: Number,
		isLoadingComments: Boolean,
	},
	computed: {
		...mapGetters(['isMobile', 'isAdmin']),
		...mapGetters('slideshow', ['getSlidePositionById']),
		currentSlideOrderNumber() {
			return this.getSlidePositionById(this.currentSlideId) + 1;
		}
	},
	methods: {
		onCommentsUpdated(comments) {
			this.$emit('annotationsUpdated', comments);
		},
	}
};
</script>
