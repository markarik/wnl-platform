<template>
	<div class="qna-thread" :class="{'is-mobile': isMobile}">
		<div ref="highlight" class="qna-question">
			<div class="qna-meta qna-question__header">
				<wnl-user-generated-content-header
					resolvable
					:author="author"
					:can-delete="(isCurrentUserAuthor && !readOnly) || $moderatorFeatures.isAllowed('access')"
					:delete-target="deleteTarget"
					:delete-resource-rotue="resourceRoute"
					:content="question"
					@resolveResource="resolveQuestion(id)"
					@unresolveResource="unresolveQuestion(id)"
					@verify="verifyQuestion(id)"
					@unverify="unverifyQuestion(id)"
				/>
				<wnl-bookmark
					:reactable-id="questionId"
					:reactable-resource="reactableResource"
					:state="bookmarkState"
					:reactions-disabled="reactionsDisabled"
					module="qna"
				/>
			</div>
			<div class="qna-question__content" v-html="content" />
			<slot name="context" />
			<div class="qna-question__actions">
				<wnl-vote
					class="qna-question__actions__action"
					type="up"
					:reactable-id="questionId"
					:reactable-resource="reactableResource"
					:state="voteState"
					module="qna"
				/>
				<template v-if="!readOnly">
					<a
						v-if="!showAnswerForm"
						class="button is-small qna-question__actions__action"
						@click="showAnswerForm = true"
					>
						<span>Odpowiedz</span>
						<span class="icon is-small answer-icon">
							<i class="fa fa-comment-o" />
						</span>
					</a>
					<a
						v-else
						class="button is-small qna-question__actions__action"
						@click="showAnswerForm = false"
					>
						<span>Ukryj</span>
					</a>
				</template>
				<wnl-watch
					class="qna-question__actions__action"
					:reactable-id="questionId"
					:reactable-resource="reactableResource"
					:state="watchState"
					:reactions-disabled="reactionsDisabled"
					module="qna"
				/>
			</div>
		</div>
		<transition name="fade">
			<wnl-qna-new-answer-form
				v-if="showAnswerForm"
				:question-id="id"
				@submitSuccess="onSubmitSuccess"
			/>
		</transition>
		<div v-if="hasAnswers" :class="{'qna-answers': true, 'disabled': question.resolved}">
			<div class="qna-question__answers-counter">
				<span class="qna-question__answers-counter__text">Odpowiedzi: {{allAnswers.length}}</span>
			</div>
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
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

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
		display: flex
		flex-direction: column

		&__header
			display: flex
			align-items: flex-start
			margin-bottom: $margin-base

			@media #{$media-query-tablet}
				align-items: center

		&__content
			font-size: $font-size-plus-1
			justify-content: flex-start
			padding-right: $margin-base
			width: 100%
			word-wrap: break-word
			word-break: break-word
			margin-bottom: $margin-big

			strong
				font-weight: $font-weight-black

		&__actions
			display: flex

			&__action
				margin-right: $margin-base
				@include action-button

				&:last-child
					margin-right: 0

		&__answers-counter
			font-weight: 600
			color: $color-gray
			text-align: center
			position: relative

			&:before
				left: 0
				top: 50%
				content: ' '
				border-bottom: 1px solid $color-light-gray
				width: 100%
				position: absolute
				z-index: -1

			&__text
				display: inline-block
				background-color: $color-white
				padding: 0 $margin-medium

	.qna-answers
		margin: $margin-base
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
</style>

<script>
import _ from 'lodash';
import { mapGetters, mapActions } from 'vuex';

import WnlUserGeneratedContentHeader from 'js/components/UserGeneratedContentHeader';
import NewAnswerForm from 'js/components/qna/NewAnswerForm';
import QnaAnswer from 'js/components/qna/QnaAnswer';
import Vote from 'js/components/global/reactions/Vote';
import Bookmark from 'js/components/global/reactions/Bookmark';
import highlight from 'js/mixins/highlight';
import Watch from 'js/components/global/reactions/Watch';
import moderatorFeatures from 'js/perimeters/moderator';

export default {
	components: {
		WnlUserGeneratedContentHeader,
		'wnl-vote': Vote,
		'wnl-qna-answer': QnaAnswer,
		'wnl-qna-new-answer-form': NewAnswerForm,
		'wnl-bookmark': Bookmark,
		'wnl-watch': Watch,
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
		};
	},
	computed: {
		...mapGetters('qna', [
			'profile',
			'getQuestion',
			'questionAnswersFromHighestUpvoteCount',
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
		...mapActions('qna', ['fetchQuestion', 'removeQuestion', 'resolveQuestion', 'unresolveQuestion', 'verifyQuestion', 'unverifyQuestion']),
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
