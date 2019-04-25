<template>
	<article class="wnl-comment">
		<wnl-user-generated-content-header
			class="wnl-comment__header"
			:author="profile"
			:content="comment"
			:can-delete="(isCurrentUserAuthor && !readOnly) || $moderatorFeatures.isAllowed('access')"
			:delete-target="target"
			:delete-resource-rotue="requestRoute"
			@deleteSuccess="$emit('deleteSuccess')"
			@verify="$emit('verify')"
			@unverify="$emit('unverify')"
		/>
		<div class="wrap content" v-html="comment.text" />
		<div class="qna-answer__actions">
			<wnl-vote
				type="up"
				:reactable-id="id"
				reactable-resource="comments"
				:state="voteState"
				module="comments"
			/>
		</div>
	</article>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-comment
		border-bottom: $border-light-gray
		padding-bottom: $margin-big

		&__header
			margin: $margin-base 0
</style>

<script>
import { mapGetters } from 'vuex';

import WnlUserGeneratedContentHeader from 'js/components/UserGeneratedContentHeader';
import WnlVote from 'js/components/global/reactions/Vote';
import moderatorFeatures from 'js/perimeters/moderator';

export default {
	name: 'Comment',
	components: {
		WnlVote,
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
};
</script>
