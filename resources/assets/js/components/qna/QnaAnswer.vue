<template>
	<div ref="highlight" class="qna-answer-container">
		<div class="qna-answer">
			<wnl-vote
				type="up"
				:reactable-id="id"
				:reactable-resource="reactableResource"
				:state="upvoteState"
				module="qna"
			></wnl-vote>
			<div class="qna-container">
				<div class="qna-wrapper">
					<div class="qna-answer-content content" v-html="content"></div>
				</div>
				<div class="qna-meta">
					<div
						class="modal-activator"
						:class="{'author-forgotten': author.deleted_at}"
						@click="showModal"
					>
						<wnl-avatar
							class="avatar"
							:full-name="author.full_name"
							:url="author.avatar"
							size="medium"
						>
						</wnl-avatar>
						<span class="qna-meta-info">
							{{author.full_name}} ·
						</span>
					</div>
					<span class="qna-meta-info">
						{{time}}
					</span>
					<span v-if="(isCurrentUserAuthor && !readOnly) || $moderatorFeatures.isAllowed('access')">
						&nbsp;·&nbsp;<wnl-delete
							:target="deleteTarget"
							:request-route="resourceRoute"
							@deleteSuccess="onDeleteSuccess"
						></wnl-delete>
					</span>
				</div>
			</div>
		</div>
		<div class="qna-answer-comments">
			<wnl-comments-list
				commentable-resource="qna_answers"
				url-param="qna_answer"
				module="qna"
				:read-only="readOnly"
				:commentable-id="id"
				:hide-watchlist="true"
				:is-unique="false"
			>
			</wnl-comments-list>
		</div>
		<wnl-modal v-if="isVisible" @closeModal="closeModal">
			<wnl-user-profile-modal :author="author" />
		</wnl-modal>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.qna-answer-container
		border-top: $border-light-gray
		margin-bottom: $margin-big

	.qna-answer-content
		word-wrap: break-word
		word-break: break-word
		justify-content: flex-start
		width: 100%

	.qna-answer
		padding: 0 $margin-base
		margin-top: $margin-base

	.modal-activator
		display: flex
		flex-direction: row
		cursor: pointer
		align-items: center
		color: $color-sky-blue
		&.author-forgotten
			color: $color-gray
			pointer-events: none

	.qna-answer-comments
		margin-left: 60px

		.qna-title.is-expanded
			padding-bottom: $margin-small
			border-bottom: $border-light-gray

	.qna-title
		font-size: $font-size-minus-1

	.qna-wrapper
		display: flex
		align-items: flex-start

	.qna-bookmark
		justify-content: flex-end

</style>

<script>
import _ from 'lodash';
import { mapGetters, mapActions } from 'vuex';

import UserProfileModal from 'js/components/users/UserProfileModal';
import Avatar from 'js/components/global/Avatar';
import Delete from 'js/components/global/form/Delete';
import Vote from 'js/components/global/reactions/Vote';
import highlight from 'js/mixins/highlight';
import CommentsList from 'js/components/comments/CommentsList';
import moderatorFeatures from 'js/perimeters/moderator';
import Modal from 'js/components/global/Modal';

import { timeFromS } from 'js/utils/time';

export default {
	name: 'QnaAnswer',
	components: {
		'wnl-avatar': Avatar,
		'wnl-delete': Delete,
		'wnl-vote': Vote,
		'wnl-comments-list': CommentsList,
		'wnl-modal': Modal,
		'wnl-user-profile-modal': UserProfileModal
	},
	perimeters: [moderatorFeatures],
	mixins: [ highlight ],
	props: ['answer', 'questionId', 'reactableId', 'readOnly', 'refresh'],
	data() {
		return {
			loading: false,
			reactableResource: 'qna_answers',
			highlightableResources: ['qna_answer', 'qna_question', 'reaction'],
			isVisible: false
		};
	},
	computed: {
		...mapGetters('qna', [
			'profile',
			'getReaction'
		]),
		...mapGetters(['currentUserId', 'isOverlayVisible']),
		id() {
			return this.answer.id;
		},
		resourceRoute() {
			return `qna_answers/${this.id}`;
		},
		content() {
			return this.answer.text;
		},
		time() {
			return timeFromS(this.answer.created_at);
		},
		author() {
			return this.profile(this.answer.profiles[0]);
		},
		isCurrentUserAuthor() {
			return this.currentUserId === this.author.user_id;
		},
		deleteTarget() {
			return 'tę odpowiedź';
		},
		upvoteState() {
			return this.getReaction(this.reactableResource, this.answer.id, 'upvote');
		},
		isAnswerInUrl() {
			return _.get(this.$route.query, 'qna_answer') == this.answer.id
					&& _.get(this.$route.query, 'qna_question') == this.questionId;
		},
		isReactionInUrl() {
			return _.get(this.$route.query, 'qna_answer') == this.answer.id
					&& _.get(this.$route.query, 'reaction');
		},
		shouldHighlight() {
			return this.isAnswerInUrl || this.isReactionInUrl;
		}
	},
	methods: {
		...mapActions('qna', ['removeAnswer']),
		showModal() {
			this.isVisible = true;
		},
		closeModal() {
			this.isVisible = false;
		},
		onDeleteSuccess() {
			this.removeAnswer({
				questionId: this.questionId,
				answerId: this.id,
			});
		},
		refreshAnswer() {
			return this.refresh();
		}
	},
	mounted() {
		if (this.shouldHighlight) {
			!this.isOverlayVisible && this.scrollAndHighlight();
		}
	},
	watch: {
		'$route' () {
			if (this.shouldHighlight) {
				this.refreshAnswer()
					.then(() => {
						!this.isOverlayVisible && this.scrollAndHighlight();
					});
			}
		},
		'isOverlayVisible' () {
			if (!this.isOverlayVisible && this.shouldHighlight) {
				this.scrollAndHighlight();
			}
		}
	}
};
</script>
