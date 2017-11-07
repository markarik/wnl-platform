<template>
	<div class="qna-answer-container" ref="highlight">
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
					<div class="qna-answer-content content" v-html="content"></div>
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
					<span v-if="(isCurrentUserAuthor && !readOnly) || $moderatorFeatures.isAllowed('access')">
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
			<wnl-comments-list
				commentableResource="qna_answers"
				urlParam="qna_answer"
				module="qna"
				:commentableId="this.id"
				:hideWatchlist="true"
				:isUnique="false"
			>
			</wnl-comments-list>
		</div>
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
	import _ from 'lodash'
	import { mapGetters, mapActions } from 'vuex'

	import Delete from 'js/components/global/form/Delete'
	import Vote from 'js/components/global/reactions/Vote'
	import highlight from 'js/mixins/highlight'
	import CommentsList from 'js/components/comments/CommentsList'
	import moderatorFeatures from 'js/perimeters/moderator'

	import { timeFromS } from 'js/utils/time'

	export default {
		name: 'QnaAnswer',
		components: {
			'wnl-delete': Delete,
			'wnl-vote': Vote,
			'wnl-comments-list': CommentsList
		},
		perimeters: [moderatorFeatures],
		mixins: [ highlight ],
		props: ['answer', 'questionId', 'reactableId', 'readOnly', 'refresh'],
		data() {
			return {
				loading: false,
				reactableResource: "qna_answers",
				highlightableResources: ["qna_answer", "qna_question", "reaction"]
			}
		},
		computed: {
			...mapGetters('qna', [
				'profile',
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
			isCurrentUserAuthor() {
				return this.currentUserId === this.author.id
			},
			deleteTarget() {
				return 'tę odpowiedź'
			},
			upvoteState() {
				return this.getReaction(this.reactableResource, this.answer.id, "upvote")
			},
			isAnswerInUrl() {
				return _.get(this.$route.query, 'qna_answer') == this.answer.id
					&& _.get(this.$route.query, 'qna_question') == this.questionId
			},
			isReactionInUrl() {
				return _.get(this.$route.query, 'qna_answer') == this.answer.id
					&& _.get(this.$route.query, 'reaction')
			},
			shouldHighlight() {
				return this.isAnswerInUrl || this.isReactionInUrl
			}
		},
		methods: {
			...mapActions('qna', ['removeAnswer']),
			onDeleteSuccess() {
				this.removeAnswer({
					questionId: this.questionId,
					answerId: this.id,
				})
			},
			refreshAnswer() {
				return this.refresh()
			}
		},
		mounted() {
			if (this.shouldHighlight) {
				!this.isOverlayVisible && this.scrollAndHighlight()
			}
		},
		watch: {
			'$route' (newRoute, oldRoute) {
				if (this.shouldHighlight) {
					this.refreshAnswer()
					.then(() => {
						!this.isOverlayVisible && this.scrollAndHighlight()
					})
				}
			},
			'isOverlayVisible' () {
				if (!this.isOverlayVisible && this.shouldHighlight) {
					this.scrollAndHighlight()
				}
			}
		}
	}
</script>
