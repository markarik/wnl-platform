<template>
	<article class="wnl-comment media">
		<div class="wnl-comment-side">
			<figure class="media-left">
				<p class="image is-32x32">
					<wnl-avatar size="medium"
						:fullName="profile.full_name"
						:url="profile.avatar"
						:userId="userId">
					</wnl-avatar>
				</p>
			</figure>
			<wnl-vote type="up" :reactableId="id" reactableResource="comments" :state="voteState" module="comments"/>
		</div>
		<div class="media-content comment-content">
			<span class="author">{{ nameToDisplay }}</span>
			<div class="comment-text wrap content" v-html="comment.text"></div>
			<small>{{time}}</small>
			<span v-if="isCurrentUserAuthor || $moderatorFeatures.isAllowed('access')">
				&nbsp;·
				<wnl-delete :requestRoute="requestRoute" :target="target" @deleteSuccess="onDeleteSuccess"></wnl-delete>
			</span>
			<wnl-resolve :resource="comment" @resolveResource="$emit('resolveComment', id)" @unresolveResource="$emit('unresolveComment', id)" />
		</div>
	</article>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.media-left
		margin-bottom: $margin-small

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

	.author
		font-weight: $font-weight-bold

	.comment-icon-link
		.icon
			font-size: $font-size-minus-3
			vertical-align: middle
</style>

<script>
import { mapGetters } from 'vuex'

import Avatar from 'js/components/global/Avatar'
import Delete from 'js/components/global/form/Delete'
import Resolve from 'js/components/global/form/Resolve'
import { timeFromS } from 'js/utils/time'
import moderatorFeatures from 'js/perimeters/moderator'
import Vote from 'js/components/global/reactions/Vote'

export default {
	name: 'Comment',
	components: {
		'wnl-avatar': Avatar,
		'wnl-delete': Delete,
		'wnl-resolve': Resolve,
		'wnl-vote': Vote,
	},
	perimeters: [moderatorFeatures],
	props: ['comment', 'profile'],
	computed: {
		...mapGetters(['currentUserId']),
		...mapGetters('comments', ['getReaction']),
		id() {
			return this.comment.id
		},
		userId() {
			return this.profile.user_id
		},
		nameToDisplay() {
			return this.profile.display_name || this.profile.full_name
		},
		time() {
			return timeFromS(this.comment.created_at)
		},
		requestRoute() {
			return `comments/${this.id}`
		},
		target() {
			return 'ten komentarz'
		},
		isCurrentUserAuthor() {
			return this.profile.user_id === this.currentUserId
		},
		voteState() {
			return this.getReaction('comments', this.id, "upvote")
		},
	},
	methods: {
		onDeleteSuccess() {
			this.$emit('removeComment', this.id)
		}
	}
}
</script>
