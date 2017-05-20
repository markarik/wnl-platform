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
						{{author.username}} ·
					</span>
					<span class="qna-meta-info">
						{{timestamp}}
					</span>
				</div>
			</div>
		</div>
		<div class="qna-answer-comments">
			<p class="qna-title">
				<span class="icon is-small comment-icon"><i class="fa fa-comment-o"></i></span>
				Komentarze ({{comments.length}})
				<span v-if="comments.length > 0"> · <a class="comments-show-link" @click="toggleComments">
					Pokaż wszystkie
				</a></span>
			</p>
			<wnl-qna-comment v-if="showComments"
				v-for="comment in comments" :comment="comment">
			</wnl-qna-comment>
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
</style>

<script>
	import { mapGetters, mapActions } from 'vuex'

	import Vote from 'js/components/qna/Vote'
	import QnaComment from 'js/components/qna/QnaComment'

	export default {
		name: 'QnaAnswer',
		components: {
			'wnl-vote': Vote,
			'wnl-qna-comment': QnaComment,
		},
		props: ['answer'],
		data() {
			return {
				loading: true,
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
			timestamp() {
				return this.answer.created_at
			},
			author() {
				return this.profile(this.answer.profiles[0])
			},
			comments() {
				return this.answerComments(this.id)
			},
		},
		methods: {
			...mapActions('qna', ['fetchComments']),
			toggleComments() {
				this.fetchComments(this.id)
					.then(() => {
						this.showComments = true
					})
			},
		},
	}
</script>
