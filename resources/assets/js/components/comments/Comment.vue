<template>
	<article class="wnl-comment media">
		<div class="wnl-comment-side">
			<wnl-user-generated-content-header
				:author="profile"
				:content="comment"
				:can-delete="(isCurrentUserAuthor && !readOnly) || $moderatorFeatures.isAllowed('access')"
				:delete-target="target"
				:delete-resource-rotue="requestRoute"
				@deleteSuccess="$emit('deleteSuccess')"
				@verify="$emit('verify')"
				@unverify="$emit('unverify')"
			/>
		</div>
		<div class="media-content comment-content">
			<div class="comment-text wrap content" v-html="comment.text" />
		</div>
	</article>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.author
		color: $color-sky-blue
		cursor: pointer

	.comment-content
		margin-top: -$margin-small

		.comment-text
			margin: $margin-small 0
			padding: 0

	.wnl-comment-side
		align-items: center
		display: flex
		flex-direction: column
		margin-right: $margin-small

		.media-left
			margin-right: 0
			margin-bottom: $margin-small
			.avatar-activator
				cursor: pointer
				&.author-forgotten
					pointer-events: none

	.author
		font-weight: $font-weight-bold
		&.author-forgotten
			color: $color-gray
			pointer-events: none

	.comment-icon-link
		.icon
			font-size: $font-size-minus-3
			vertical-align: middle
</style>

<script>
import { mapGetters } from 'vuex';

import WnlUserGeneratedContentHeader from 'js/components/UserGeneratedContentHeader';
import moderatorFeatures from 'js/perimeters/moderator';

export default {
	name: 'Comment',
	components: {
		WnlUserGeneratedContentHeader
	},
	perimeters: [moderatorFeatures],
	props: ['comment', 'profile'],
	data() {
		return {
			isVisible: false
		};
	},
	computed: {
		...mapGetters(['currentUserId']),
		...mapGetters('comments', ['getReaction']),
		id() {
			return this.comment.id;
		},
		requestRoute() {
			return `comments/${this.id}`;
		},
		target() {
			return 'ten komentarz';
		},
		isCurrentUserAuthor() {
			return this.profile.user_id === this.currentUserId;
		},
		voteState() {
			return this.getReaction('comments', this.id, 'upvote');
		},
	},
	methods: {
		showModal() {
			this.isVisible = true;
		},
		closeModal() {
			this.isVisible = false;
		},
		onDeleteSuccess() {
			this.$emit('removeComment', this.id);
		}
	}
};
</script>
