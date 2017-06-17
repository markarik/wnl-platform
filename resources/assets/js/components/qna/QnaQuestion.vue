<template>
	<div>
		<div class="question-loader" v-if="loading">
			<wnl-text-loader></wnl-text-loader>
		</div>
		<div class="qna-question">
			<div class="votes">
				<wnl-vote type="up" count="0" :reactableId="questionId" reactableResource="qna_questions" module="qna"></wnl-vote>
			</div>
			<div class="qna-container">
				<div class="qna_wrapper">
					<div class="qna-question-content" v-html="content"></div>
					<wnl-bookmark class="qna_bookmark" :reactableId="questionId" reactableResource="qna_questions" module="qna"></wnl-bookmark>
				</div>
				<div class="tags" v-if="tags.length > 0">
					<span v-for="tag, key in tags" class="tag is-light" v-text="tag"></span>
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
				<div class="qna-answers">
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
					<a class="button is-small is-wide qna-answers-show-all"
						v-if="!allAnswers && otherAnswers.length > 0"
						@click="allAnswers = true">
						Pokaż pozostałe odpowiedzi
					</a>
				</div>
			</div>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.button .icon.answer-icon
		margin-left: $margin-small
		margin-right: $margin-tiny

	.question-loader
		border-top: $border-light-gray
		margin: $margin-big
		padding: $margin-big

	.qna-question
		border-top: $border-light-gray
		margin: $margin-base 0
		padding: $margin-big 0

	.qna-question-content
		word-wrap: break-word
		word-break: break-word
		font-weight: $font-weight-bold
		font-size: $font-size-plus-1
		justify-content: flex-start
		width: 100%

		strong
			font-weight: $font-weight-black

	.qna-answers
		margin-top: $margin-big

	.qna-answers-show-all
		margin-top: $margin-huge

	.qna_wrapper
		display: flex
		align-items: flex-start

	.qna_bookmark
		justify-content: flex-end

	.tag
		margin-right: $margin-small

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

	export default {
		name: 'QnaQuestion',
		components: {
			'wnl-delete': Delete,
			'wnl-vote': Vote,
			'wnl-qna-answer': QnaAnswer,
			'wnl-qna-new-answer-form': NewAnswerForm,
			'wnl-bookmark': Bookmark,
		},
		props: ['questionId', 'readOnly'],
		data() {
			return {
				allAnswers: false,
				loading: false,
				showAnswerForm: false,
			}
		},
		computed: {
			...mapGetters('qna', [
				'profile',
				'getQuestion',
				'questionAnswersFromHighestUpvoteCount',
				'questionTags',
			]),
			...mapGetters(['currentUserId']),
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
	}
</script>
