<template>
	<div :class="{'isHighlighted': isHighlighted, 'qna-answer-container': true}" ref="answer">
		<div class="qna-answer">
			<div class="votes">
				<wnl-vote
					type="up"
					:reactableId="id"
					:reactableResource="reactableResource"
					:state="upvoteState"
					module="qna"
				></wnl-vote>
			</div>
			<div class="qna-container">
				<div class="qna-wrapper">
					<div class="qna-answer-content" v-html="content"></div>
				</div>
				<div class="qna-meta">
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
		<div class="qna-answer-comments">
			<p class="qna-title">
				<span class="icon is-small comment-icon"><i class="fa fa-comments-o"></i></span>
				Komentarze ({{comments.length}})
				<span v-if="comments.length > 0">
					 · <a class="secondary-link" @click="toggleComments" v-text="toggleCommentsText"></a>
				</span>
				<span v-if="!readOnly">
					 · <a class="secondary-link" @click="showCommentForm = !showCommentForm">Skomentuj</a>
				</span>
			</p>
			<transition name="fade">
				<wnl-qna-new-comment-form v-if="showCommentForm"
					:answerId="this.id"
					@submitSuccess="onSubmitSuccess">
				</wnl-qna-new-comment-form>
			</transition>
			<wnl-qna-comment v-if="showComments"
				v-for="comment in comments"
				:answerId="id"
				:comment="comment"
				:key="comment.id"
				:readOnly="readOnly">
			</wnl-qna-comment>
			<div class="comments-loader" v-if="loading">
				<wnl-text-loader></wnl-text-loader>
			</div>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.qna-answer-container
		border-top: $border-light-gray
		margin-bottom: $margin-big

		&.isHighlighted
			@keyframes colorchange
				0%
					background: blue
				100%
					background: initial

			animation: colorchange 5s

	.qna-answer-content
		word-wrap: break-word
		word-break: break-word
		justify-content: flex-start
		width: 100%

	.qna-answer
		// background: $color-background-lighter-gray
		padding: 0 $margin-base
		margin-top: $margin-base

	.qna-answer-comments
		margin-left: $margin-humongous

	.qna-title
		font-size: $font-size-minus-1

	.comment-icon
		margin-right: $margin-small
		margin-top: -$margin-small

	.comments-loader
		margin: $margin-base 0

	.qna-wrapper
		display: flex
		align-items: flex-start

	.qna-bookmark
		justify-content: flex-end
</style>

<script>
	import _ from 'lodash'
	import { mapGetters, mapActions } from 'vuex'

	import Delete from 'js/components/global/form/Delete'
	import NewCommentForm from 'js/components/qna/NewCommentForm'
	import QnaComment from 'js/components/qna/QnaComment'
	import Vote from 'js/components/global/reactions/Vote'

	import { timeFromS } from 'js/utils/time'
	import { scrollToElement } from 'js/utils/animations'

	export default {
		name: 'QnaAnswer',
		components: {
			'wnl-delete': Delete,
			'wnl-qna-new-comment-form': NewCommentForm,
			'wnl-qna-comment': QnaComment,
			'wnl-vote': Vote,
		},
		props: ['answer', 'questionId', 'reactableId', 'module', 'readOnly'],
		data() {
			return {
				commentsFetched: false,
				loading: false,
				showComments: false,
				showCommentForm: false,
				reactableResource: "qna_answers"
			}
		},
		computed: {
			...mapGetters('qna', [
				'profile',
				'answerComments',
				'getReaction'
			]),
			...mapGetters(['currentUserId', 'isOverlayVisible']),
			id() {
				return this.answer.id
			},
			resourceRoute() {
				return `qna_answers/${this.id}`
			},
			content() {
				return this.answer.text
			},
			time() {
				return timeFromS(this.answer.created_at)
			},
			author() {
				return this.profile(this.answer.profiles[0])
			},
			comments() {
				return this.answerComments(this.id)
			},
			toggleCommentsText() {
				return this.showComments ? 'Schowaj' : 'Pokaż'
			},
			isCurrentUserAuthor() {
				return this.currentUserId === this.author.id
			},
			deleteTarget() {
				return 'tę odpowiedź'
			},
			upvoteState() {
				return this.getReaction(this.reactableResource, this.answer.id, "upvote")
			},
			isHighlighted() {
				return !this.isOverlayVisible && _.get(this.$route.query, 'qna_answer') == this.answer.id
			}
		},
		methods: {
			...mapActions('qna', ['fetchComments', 'removeAnswer']),
			toggleComments() {
				if (!this.commentsFetched) {
					this.dispatchFetchComments()
				} else {
					this.showComments = !this.showComments
				}
			},
			dispatchFetchComments() {
				this.loading = true
				return this.fetchComments(this.id)
					.then(() => {
						this.commentsFetched = true
						this.showComments = true
						this.loading = false
					})
			},
			onSubmitSuccess() {
				this.showComments = true
				this.showCommentForm = false
				this.dispatchFetchComments()
			},
			onDeleteSuccess() {
				this.removeAnswer({
					questionId: this.questionId,
					answerId: this.id,
				})
			},
			scrollToAndShowCommentsIfHighlighted() {
				!this.isOverlayVisible && this.isHighlighted && this.dispatchFetchComments()
					.then(() => scrollToElement(this.$refs.answer, 150)
				)
			}
		},
		mounted() {
			this.scrollToAndShowCommentsIfHighlighted()
		},
		watch: {
			'$route' (newRoute, oldRoute) {
				this.scrollToAndShowCommentsIfHighlighted()
			},
			'isOverlayVisible' () {
				this.scrollToAndShowCommentsIfHighlighted()
			}
		}
	}
</script>
