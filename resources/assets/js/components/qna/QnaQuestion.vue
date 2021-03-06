<template>
	<div class="qna-thread" :class="{'is-mobile': isMobile}">
		<div ref="highlight" class="qna-question">
			<wnl-vote
				type="up"
				:reactable-id="questionId"
				:reactable-resource="reactableResource"
				:state="voteState"
				module="qna"
			/>
			<div class="qna-container">
				<div class="qna-wrapper">
					<div class="qna-question-content content" v-html="content" />
					<wnl-bookmark
						class="qna-bookmark"
						:reactable-id="questionId"
						:reactable-resource="reactableResource"
						:state="bookmarkState"
						:reactions-disabled="reactionsDisabled"
						module="qna"
					/>
				</div>
				<div v-if="tags.length > 0" class="tags">
					<span
						v-for="(tag, key) in tags"
						:key="key"
						class="tag is-light"
					>
						<span>{{tag}}</span>
					</span>
				</div>
				<div class="qna-question-meta qna-meta">
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
						/>
						<span class="qna-meta-info">
							{{author.full_name}}
						</span>
					</div>
					<span class="qna-meta-info">
						· {{time}}
					</span>
					<span v-if="(isCurrentUserAuthor && !readOnly) || $moderatorFeatures.isAllowed('access')">
						&nbsp;·&nbsp;<wnl-delete
							:target="deleteTarget"
							:request-route="resourceRoute"
							@deleteSuccess="onDeleteSuccess"
						/>
					</span>
					<wnl-resolve
						:resource="question"
						@resolveResource="resolveQuestion(id)"
						@unresolveResource="unresolveQuestion(id)"
					/>
				</div>
				<slot name="context" />
			</div>
		</div>
		<div :class="{'qna-answers': true, 'disabled': question.resolved}">
			<div class="level">
				<div class="level-left qna-answers-heading">
					<p>Odpowiedzi ({{answersFromHighestUpvoteCount.length}})</p>
					<wnl-watch
						:reactable-id="questionId"
						:reactable-resource="reactableResource"
						:state="watchState"
						:reactions-disabled="reactionsDisabled"
						module="qna"
					/>
				</div>
				<div v-if="!readOnly" class="level-right">
					<a
						v-if="!showAnswerForm"
						class="button is-small"
						@click="showAnswerForm = true"
					>
						<span>Odpowiedz</span>
						<span class="icon is-small answer-icon">
							<i class="fa fa-comment-o" />
						</span>
					</a>
					<a
						v-if="showAnswerForm"
						class="button is-small"
						@click="showAnswerForm = false"
					>
						<span>Ukryj</span>
					</a>
				</div>
			</div>
			<transition name="fade">
				<wnl-qna-new-answer-form
					v-if="showAnswerForm"
					:question-id="id"
					@submitSuccess="onSubmitSuccess"
				/>
			</transition>
			<wnl-qna-answer
				v-if="hasAnswers && !showAllAnswers"
				:answer="latestAnswer"
				:question-id="questionId"
				:read-only="readOnly"
				:refresh="refreshQuestionAndShowAnswers"
			/>
			<wnl-qna-answer
				v-for="answer in allAnswers"
				v-else-if="showAllAnswers"
				:key="answer.id"
				:answer="answer"
				:question-id="questionId"
				:read-only="readOnly"
				:refresh="refreshQuestionAndShowAnswers"
			/>
			<a
				v-if="!showAllAnswers && otherAnswers.length > 0"
				class="qna-answers-show-all"
				@click="showAllAnswers = true"
			>
				<span class="icon is-small"><i class="fa fa-angle-down" /></span> Pokaż pozostałe odpowiedzi ({{otherAnswers.length}})
			</a>
		</div>
		<wnl-modal v-if="isVisible" @closeModal="closeModal">
			<wnl-user-profile-modal :author="author" />
		</wnl-modal>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.qna-thread
		border: $border-light-gray
		margin-bottom: $margin-huge

	.button .icon.answer-icon
		margin-left: $margin-small
		margin-right: $margin-tiny

	.qna-question
		background: $color-background-lighter-gray
		border-bottom: $border-light-gray
		padding: $margin-base

	.modal-activator
		display: flex
		flex-direction: row
		cursor: pointer
		align-items: center
		color: $color-sky-blue

		&.author-forgotten
			color: $color-gray
			pointer-events: none

	.qna-question-content
		font-size: $font-size-plus-1
		justify-content: flex-start
		padding-right: $margin-base
		width: 100%
		word-wrap: break-word
		word-break: break-word

		strong
			font-weight: $font-weight-black

	.qna-answers-heading
		color: $color-gray

	.qna-answers
		margin: $margin-base $margin-huge $margin-huge $margin-huge
		position: relative

		&.disabled:before
			background: $color-white-transparent
			position: absolute
			content: ' '
			cursor: not-allowed
			height: calc(100% + #{$margin-base} + #{$margin-huge})
			left: -$margin-huge
			top: -$margin-base
			width: calc(100% + #{$margin-huge} + #{$margin-huge})
			z-index: $z-index-overlay

	.qna-thread.is-mobile
		.qna-answers
			margin: $margin-base

	.qna-answers-show-all
		display: block
		color: $color-gray
		font-size: $font-size-minus-1
		margin-top: $margin-base
		text-align: center
		text-transform: uppercase

		&:active,
		&:visited
			color: $color-gray

		&:hover
			color: $color-darkest-gray

	.qna-wrapper
		display: flex
		align-items: flex-start

	.qna-bookmark
		justify-content: flex-end

	.tag
		margin-right: $margin-small
		margin-top: $margin-small
</style>

<script>
import _ from 'lodash';
import { mapGetters, mapActions } from 'vuex';

import UserProfileModal from 'js/components/users/UserProfileModal';
import Delete from 'js/components/global/form/Delete';
import Resolve from 'js/components/global/form/Resolve';
import NewAnswerForm from 'js/components/qna/NewAnswerForm';
import QnaAnswer from 'js/components/qna/QnaAnswer';
import Vote from 'js/components/global/reactions/Vote';
import Bookmark from 'js/components/global/reactions/Bookmark';
import highlight from 'js/mixins/highlight';
import Watch from 'js/components/global/reactions/Watch';
import Modal from 'js/components/global/Modal';
import moderatorFeatures from 'js/perimeters/moderator';
import { timeFromS } from 'js/utils/time';

export default {
	components: {
		'wnl-delete': Delete,
		'wnl-resolve': Resolve,
		'wnl-vote': Vote,
		'wnl-qna-answer': QnaAnswer,
		'wnl-qna-new-answer-form': NewAnswerForm,
		'wnl-bookmark': Bookmark,
		'wnl-watch': Watch,
		'wnl-modal': Modal,
		'wnl-user-profile-modal': UserProfileModal,
	},
	mixins: [highlight],
	perimeters: [moderatorFeatures],
	props: ['questionId', 'readOnly', 'reactionsDisabled', 'config'],
	data() {
		return {
			showAllAnswers: false,
			showAnswerForm: false,
			reactableResource: 'qna_questions',
			highlightableResources: ['qna_question', 'reaction'],
			isVisible: false,
		};
	},
	computed: {
		...mapGetters('qna', [
			'profile',
			'getQuestion',
			'questionAnswersFromHighestUpvoteCount',
			'questionTags',
			'getReaction',
			'questionAnswers',
			'answer'
		]),
		...mapGetters(['currentUserId', 'isMobile', 'isOverlayVisible']),
		question() {
			return this.getQuestion(this.questionId);
		},
		id() {
			return this.questionId;
		},
		content() {
			return this.question.text;
		},
		author() {
			return this.profile(this.question.profiles[0]);
		},
		isCurrentUserAuthor() {
			return this.currentUserId === this.author.user_id;
		},
		resourceRoute() {
			return `qna_questions/${this.id}`;
		},
		deleteTarget() {
			return 'to pytanie';
		},
		time() {
			return timeFromS(this.question.created_at);
		},
		answersFromHighestUpvoteCount() {
			return this.questionAnswersFromHighestUpvoteCount(this.id);
		},
		hasAnswers() {
			return this.answersFromHighestUpvoteCount.length > 0;
		},
		latestAnswer() {
			if (this.config.highlighted[this.id]) {
				const answerId = this.config.highlighted[this.id];
				return this.answer(answerId);
			} else {
				return _.head(this.answersFromHighestUpvoteCount) || {};
			}
		},
		otherAnswers() {
			return _.tail(this.answersFromHighestUpvoteCount) || [];
		},
		allAnswers() {
			return this.answersFromHighestUpvoteCount;
		},
		tags() {
			return this.questionTags(this.questionId).map((tag) => tag.name) || [];
		},
		bookmarkState() {
			return this.getReaction(this.reactableResource, this.questionId, 'bookmark');
		},
		watchState() {
			return this.getReaction(this.reactableResource, this.questionId, 'watch');
		},
		voteState() {
			return this.getReaction(this.reactableResource, this.questionId, 'upvote');
		},
		isQuestionInUrl() {
			return !_.get(this.$route, 'query.qna_answer') && _.get(this.$route, 'query.qna_question') == this.questionId;
		},
		answerInUrl() {
			return _.get(this.$route, 'query.qna_answer');
		},
		isQuestionAnswerInUrl() {
			if (this.isNotFetchedAnswerInUrl) return true;

			return !!this.questionAnswers(this.questionId).find((answer) => answer.id == this.answerInUrl);
		},
		isNotFetchedAnswerInUrl() {
			const questionId = _.get(this.$route, 'query.qna_question');

			return questionId == this.questionId && this.answerInUrl;
		},
	},
	watch: {
		'$route'() {
			if (!this.isOverlayVisible && this.isQuestionInUrl) {
				this.dispatchFetchQuestion()
					.then(() => this.scrollAndHighlight());
			}

			if (this.isQuestionAnswerInUrl) {
				if (this.isNotFetchedAnswerInUrl) this.dispatchFetchQuestion();
				this.showAllAnswers = true;
			}
		},
		'isOverlayVisible'() {
			if (!this.isOverlayVisible && this.isQuestionInUrl) {
				this.scrollAndHighlight();
			}
		},
	},
	mounted() {
		if (this.isQuestionAnswerInUrl) {
			this.showAllAnswers = true;
		}

		if (!this.isOverlayVisible && this.isQuestionInUrl) {
			this.scrollAndHighlight();
		}
	},
	methods: {
		...mapActions('qna', ['fetchQuestion', 'removeQuestion', 'resolveQuestion', 'unresolveQuestion']),
		showModal() {
			this.isVisible = true;
		},
		closeModal() {
			this.isVisible = false;
		},
		dispatchFetchQuestion() {
			return this.fetchQuestion(this.id)
				.catch((error) => {
					$wnl.logger.error(error);
				});
		},
		getAnswer(id) {
			return this.answersFromHighestUpvoteCount.filter(answer => answer.id == id);
		},
		hasAnswer(id) {
			return this.getAnswer(id).length > 0;
		},
		onDeleteSuccess() {
			this.removeQuestion(this.id);
		},
		onSubmitSuccess() {
			this.showAnswerForm = false;
			this.dispatchFetchQuestion();
		},
		refreshQuestionAndShowAnswers() {
			return this.dispatchFetchQuestion(() => {
				this.showAllAnswers = true;
			});
		}
	},
};
</script>
