<template>
	<div class="qna-answer-container">
		<div class="qna-answer">
			<div class="votes">
				<wnl-vote type="up" count="0"></wnl-vote>
			</div>
			<div class="qna-container">
				<div class="qna-answer-content" v-html="content"></div>
				<div class="qna-meta">
					<wnl-avatar
						:username="author.username"
						:url="author.avatar"
						size="medium">
					</wnl-avatar>
					<span class="qna-meta-info">
						{{author.full_name}} ·
					</span>
					<span class="qna-meta-info">
						{{time}}
					</span>
				</div>
			</div>
		</div>
		<div class="qna-answer-comments">
			<p class="qna-title">
				<span class="icon is-small comment-icon"><i class="fa fa-comments-o"></i></span>
				Komentarze ({{comments.length}})
				<span v-if="comments.length > 0"> ·
					<a class="comments-show-link" @click="toggleComments" v-text="toggleCommentsText"></a>
				</span>
			</p>
			<wnl-qna-comment v-if="showComments"
				v-for="comment in comments" :comment="comment">
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
		margin-bottom: $margin-huge

	.qna-answer
		background: $color-background-lighter-gray
		padding: $margin-base
		margin-top: $margin-base

	.qna-answer-comments
		margin-left: 4em

	.qna-title
		font-size: $font-size-minus-1

	.comment-icon
		margin-right: $margin-small
		margin-top: -$margin-small

	.comments-loader
		margin: $margin-base 0

	.comments-show-link,
	.comments-show-link:hover
		color: $color-gray-dimmed
</style>

<script>
	import { mapGetters, mapActions } from 'vuex'

	import Vote from 'js/components/qna/Vote'
	import QnaComment from 'js/components/qna/QnaComment'

	import { timeFromS } from 'js/utils/time'

	export default {
		name: 'QnaAnswer',
		components: {
			'wnl-vote': Vote,
			'wnl-qna-comment': QnaComment,
		},
		props: ['answer'],
		data() {
			return {
				commentsFetched: false,
				loading: false,
				showComments: false,
			}
		},
		computed: {
			...mapGetters('qna', [
				'profile',
				'answerComments',
			]),
			id() {
				return this.answer.id
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
			}
		},
		methods: {
			...mapActions('qna', ['fetchComments']),
			toggleComments() {
				if (!this.commentsFetched) {
					this.loading = true
					this.fetchComments(this.id)
					.then(() => {
						this.commentsFetched = true
						this.showComments = true
						this.loading = false
					})
				} else {
					this.showComments = !this.showComments
				}
			},
		},
	}
</script>
