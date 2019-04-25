<template>
	<div class="qna-answer">
		<div ref="highlight">
			<wnl-user-generated-content-header
				:author="author"
				:content="answer"
				:can-delete="(isCurrentUserAuthor && !readOnly) || $moderatorFeatures.isAllowed('access')"
				:delete-target="deleteTarget"
				:delete-resource-rotue="resourceRoute"
				@verify="verifyAnswer(id)"
				@unverify="unverifyAnswer(id)"
			/>
			<div class="qna-answer-content" v-html="content" />
		</div>
		<div class="qna-answer__actions">
			<wnl-vote
				class="qna-answer__actions__upvote"
				type="up"
				:reactable-id="id"
				:reactable-resource="reactableResource"
				:state="upvoteState"
				module="qna"
			/>
			<wnl-comments-list
				commentable-resource="qna_answers"
				url-param="qna_answer"
				module="qna"
				:read-only="readOnly"
				:commentable-id="id"
				:hide-watchlist="true"
				:is-unique="false"
			/>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.qna-answer
		padding-top: $margin-big
		padding-bottom: $margin-base
		border-bottom: $border-light-gray
		&:last-child
			border-bottom: none

		&__actions
			display: flex
			align-items: flex-start

			&__upvote
				margin-top: 13px
				margin-right: $margin-base

	.qna-answer-content
		margin-top: $margin-big
		word-wrap: break-word
		word-break: break-word
		justify-content: flex-start
		width: 100%

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

import WnlUserGeneratedContentHeader from 'js/components/UserGeneratedContentHeader';
import Vote from 'js/components/global/reactions/Vote';
import highlight from 'js/mixins/highlight';
import CommentsList from 'js/components/comments/CommentsList';
import moderatorFeatures from 'js/perimeters/moderator';

export default {
	name: 'QnaAnswer',
	components: {
		WnlUserGeneratedContentHeader,
		'wnl-vote': Vote,
		'wnl-comments-list': CommentsList,
	},
	perimeters: [moderatorFeatures],
	mixins: [ highlight ],
	props: ['answer', 'questionId', 'reactableId', 'readOnly', 'refresh'],
	data() {
		return {
			loading: false,
			reactableResource: 'qna_answers',
			highlightableResources: ['qna_answer', 'qna_question', 'reaction'],
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
	},
	mounted() {
		if (this.shouldHighlight) {
			!this.isOverlayVisible && this.scrollAndHighlight();
		}
	},
	methods: {
		...mapActions('qna', ['removeAnswer', 'verifyAnswer', 'unverifyAnswer']),
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
};
</script>
