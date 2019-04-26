<template>
	<article class="wnl-comment media">
		<div class="wnl-comment-side">
			<figure class="media-left">
				<div
					class="avatar-activator"
					:class="{'author-forgotten': profile.deleted_at}"
					@click="showModal"
				>
					<p class="image is-32x32">
						<wnl-avatar
							size="medium"
							:full-name="profile.full_name"
							:url="profile.avatar"
							:roles="profile.roles"
						/>
					</p>
				</div>
			</figure>
			<wnl-vote
				type="up"
				:reactable-id="id"
				reactable-resource="comments"
				:state="voteState"
				module="comments"
			/>
		</div>

		<div class="media-content comment-content">
			<span
				class="author"
				:class="{'author-forgotten': profile.deleted_at}"
				@click="showModal"
			>{{profile.full_name}}</span>

			<wnl-user-signature :roles="profile.roles" />

			<div class="comment-text wrap content" v-html="comment.text" />

			<small>{{time}}</small>
			<span v-if="isCurrentUserAuthor || $moderatorFeatures.isAllowed('access')">
				&nbsp;·
				<wnl-delete
					:request-route="requestRoute"
					:target="target"
					@deleteSuccess="onDeleteSuccess"
				/>
			</span>
			<wnl-resolve
				:resource="comment"
				@resolveResource="$emit('resolveComment', id)"
				@unresolveResource="$emit('unresolveComment', id)"
			/>
		</div>

		<wnl-modal v-if="isVisible" @closeModal="closeModal">
			<wnl-user-profile-modal :profile="profile" />
		</wnl-modal>
	</article>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.media-left


	.author
		color: $color-sky-blue
		cursor: pointer

	.comment-content

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

import UserProfileModal from 'js/components/users/UserProfileModal';
import Avatar from 'js/components/global/Avatar';
import Delete from 'js/components/global/form/Delete';
import Resolve from 'js/components/global/form/Resolve';
import { timeFromS } from 'js/utils/time';
import moderatorFeatures from 'js/perimeters/moderator';
import Vote from 'js/components/global/reactions/Vote';
import Modal from 'js/components/global/Modal.vue';
import UserSignature from 'js/components/global/UserSignature';

export default {
	name: 'Comment',
	components: {
		'wnl-avatar': Avatar,
		'wnl-delete': Delete,
		'wnl-resolve': Resolve,
		'wnl-vote': Vote,
		'wnl-modal': Modal,
		'wnl-user-profile-modal': UserProfileModal,
		'wnl-user-signature': UserSignature,
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
		time() {
			return timeFromS(this.comment.created_at);
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
