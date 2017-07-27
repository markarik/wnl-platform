<template>
	<div class="qna-thread" :class="{'is-mobile': isMobile, 'isHighlighted': isHighlighted}">
		<div class="question-loader" v-if="loading">
			<wnl-text-loader></wnl-text-loader>
		</div>
		<div :class="{'qna-question': true, 'isHighlighted': isHighlighted}">
			<div class="votes">
				<wnl-vote
					type="up"
					:reactableId="questionId"
					:reactableResource="reactableResource"
					:state="voteState"
					module="qna"
				></wnl-vote>
			</div>
			<div class="qna-container">
				<div class="qna-wrapper">
					<div class="qna-question-content" v-html="content"></div>
					<wnl-bookmark
						class="qna-bookmark"
						:reactableId="questionId"
						:reactableResource="reactableResource"
						:state="bookmarkState"
						:reactionsDisabled="reactionsDisabled"
						module="qna"
					></wnl-bookmark>
				</div>
				<div class="tags" v-if="tags.length > 0">
					<span v-for="tag, key in tags" class="tag is-light">
						<span>{{tag}}</span>
					</span>
				</div>
				<div class="qna-question-meta qna-meta">
					<wnl-avatar
							:fullName="author.full_name"
							:url="author.avatar"
							size="medium">
					</wnl-avatar>
					<span class="qna-meta-info">
						{{author.full_name}} ·
					</span>
					<span class="qna-meta-info">
						{{time}}
					</span>
					<span v-if="isCurrentUserAuthor && !readOnly">
						&nbsp;·&nbsp;<wnl-delete
							:target="deleteTarget"
							:requestRoute="resourceRoute"
							@deleteSuccess="onDeleteSuccess"
						></wnl-delete>
					</span>
				</div>
			</div>
		</div>
		 <div class="qna-answers" ref="question">
			<div class="level">
				<div class="level-left">
					<p class="text-dimmed">Odpowiedzi ({{answersFromHighestUpvoteCount.length}})</p>
				</div>
				<div class="level-right" v-if="!readOnly">
					<a class="button is-small" v-if="!showAnswerForm" @click="showAnswerForm = true">
						<span>Odpowiedz</span>
						<span class="icon is-small answer-icon">
							<i class="fa fa-comment-o"></i>
						</span>
					</a>
					<a class="button is-small" v-if="showAnswerForm" @click="showAnswerForm = false">
						<span>Ukryj</span>
					</a>
				</div>
			</div>
			<transition name="fade">
				<wnl-qna-new-answer-form v-if="showAnswerForm"
					:questionId="this.id"
					@submitSuccess="onSubmitSuccess">
				</wnl-qna-new-answer-form>
			</transition>
			<wnl-qna-answer v-if="hasAnswers" :answer="latestAnswer" :questionId="questionId" :readOnly="readOnly"></wnl-qna-answer>
			<wnl-qna-answer v-if="allAnswers"
				v-for="answer in otherAnswers"
				:answer="answer"
				:questionId="questionId"
				:key="answer.id"
				:readOnly="readOnly">
			</wnl-qna-answer>
			<a class="qna-answers-show-all"
				v-if="!allAnswers && otherAnswers.length > 0"
				@click="allAnswers = true">
				<span class="icon is-small"><i class="fa fa-angle-down"></i></span> Pokaż pozostałe odpowiedzi ({{otherAnswers.length}})
			</a>
		</div>
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

	.question-loader
		border-top: $border-light-gray
		margin: $margin-big
		padding: $margin-big

	.qna-question
		background: $color-background-lighter-gray
		border-bottom: $border-light-gray
		padding: $margin-base

	.qna-question-content
		font-size: $font-size-plus-1
		justify-content: flex-start
		padding-right: $margin-base
		width: 100%
		word-wrap: break-word
		word-break: break-word

		strong
			font-weight: $font-weight-black

	.qna-answers
		margin-left: $margin-huge
		margin-top: $margin-base
		margin: $margin-base $margin-huge $margin-huge $margin-huge

	.qna-thread.is-mobile
		.qna-answers
			margin: $margin-base

	.qna-answers-show-all
		display: block
		color: $color-gray-dimmed
		font-size: $font-size-minus-1
		margin-top: $margin-base
		text-align: center
		text-transform: uppercase

		&:active,
		&:visited
			color: $color-gray-dimmed

		&:hover
			color: $color-gray

	.qna-wrapper
		display: flex
		align-items: flex-start

	.qna-bookmark
		justify-content: flex-end

	.tag
		margin-right: $margin-small
		margin-top: $margin-small

	.isHighlighted
		@keyframes colorchange
			0%
				background: yellowgreen
			75%
				background: initial

		@-webkit-keyframes colorchange
			0%
				background: yellowgreen
			75%
				background: initial

		animation: colorchange 5s
		-webkit-animation: colorchange 5s
</style>

<script>
	import _ from 'lodash'
	import { nextTick } from 'vue'
	import { mapGetters, mapActions } from 'vuex'

	import Delete from 'js/components/global/form/Delete'
	import NewAnswerForm from 'js/components/qna/NewAnswerForm'
	import QnaAnswer from 'js/components/qna/QnaAnswer'
	import Vote from 'js/components/global/reactions/Vote'
	import Bookmark from 'js/components/global/reactions/Bookmark'

	import { timeFromS } from 'js/utils/time'
	import { scrollToElement } from 'js/utils/animations'

	export default {
		name: 'QnaQuestion',
		components: {
			'wnl-delete': Delete,
			'wnl-vote': Vote,
			'wnl-qna-answer': QnaAnswer,
			'wnl-qna-new-answer-form': NewAnswerForm,
			'wnl-bookmark': Bookmark,
		},
		props: ['questionId', 'readOnly', 'reactionsDisabled'],
		data() {
			return {
				allAnswers: false,
				loading: false,
				showAnswerForm: false,
				reactableResource: "qna_questions"
			}
		},
		computed: {
			...mapGetters('qna', [
				'profile',
				'getQuestion',
				'questionAnswersFromHighestUpvoteCount',
				'questionTags',
				'getReaction'
			]),
			...mapGetters(['currentUserId', 'isMobile']),
			question() {
				return this.getQuestion(this.questionId)
			},
			id() {
				return this.questionId
			},
			content() {
				return this.question.text
			},
			author() {
				if (this.question.hasOwnProperty('profiles')) {
					return this.profile(this.question.profiles[0])
				} else {
					this.loading = true
					this.dispatchFetchQuestion()
						.then(() => {
							return this.profile(this.question.profiles[0])
						})
				}
			},
			isCurrentUserAuthor() {
				return this.currentUserId === this.author.id
			},
			resourceRoute() {
				return `qna_questions/${this.id}`
			},
			deleteTarget() {
				return 'to pytanie'
			},
			time() {
				return timeFromS(this.question.created_at)
			},
			answersFromHighestUpvoteCount() {
				return this.questionAnswersFromHighestUpvoteCount(this.id)
			},
			hasAnswers() {
				return this.answersFromHighestUpvoteCount.length > 0
			},
			latestAnswer() {
				return _.head(this.answersFromHighestUpvoteCount) || {}
			},
			otherAnswers() {
				return _.tail(this.answersFromHighestUpvoteCount) || []
			},
			tags() {
				return this.questionTags(this.questionId).map((tag) => tag.name) || []
			},
			bookmarkState() {
				return this.getReaction(this.reactableResource, this.questionId, "bookmark")
			},
			voteState() {
				return this.getReaction(this.reactableResource, this.questionId, "upvote")
			},
			isHighlighted() {
				// dobule "=" because one is numeric and second one is string... :(
				return _.get(this.$route.query, 'qna_question') == this.questionId
			}
		},
		methods: {
			...mapActions('qna', ['fetchQuestion', 'removeQuestion']),
			dispatchFetchQuestion() {
				return this.fetchQuestion(this.id)
					.then(() => {
						this.loading = false
					})
					.catch((error) => {
						$wnl.logger.error(error)
						this.loading = false
					})
			},
			onSubmitSuccess() {
				this.showAnswerForm = false
				this.dispatchFetchQuestion()
			},
			onDeleteSuccess() {
				this.removeQuestion(this.id)
			},
		},
		mounted() {
			this.isHighlighted && scrollToElement(this.$refs.question, 0)
		}
	}
</script>
