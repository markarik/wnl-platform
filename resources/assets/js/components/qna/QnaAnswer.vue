<template>
	<div>
		<div class="qna-answer">
			<div class="votes">
				<wnl-vote type="up" count="0"></wnl-vote>
			</div>
			<div class="qna-container">
				<div class="qna-answer-content" v-html="content"></div>
				<div class="qna-meta">
					<wnl-avatar
						:username="author.username"
						:url="author.avatarUrl"
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
			<p class="qna-title">Komentarze ({{comments.length}})</p>
			<!-- <wnl-qna-comment v-for="comment in answer.comments" :comment="comment"></wnl-qna-comment> -->
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.qna-answer
		background: $color-background-lighter-gray
		padding: $margin-base
		margin-top: $margin-base

	.qna-answer-comments
		margin-left: 4em

	.qna-title
		font-size: $font-size-minus-1
</style>

<script>
	import { mapGetters } from 'vuex'

	import Vote from 'js/components/qna/Vote'
	import QnaComment from 'js/components/qna/QnaComment'

	export default {
		name: 'QnaAnswer',
		components: {
			'wnl-vote': Vote,
			'wnl-qna-comment': QnaComment,
		},
		props: ['answer'],
		computed: {
			...mapGetters('qna', [
				'profile'
			]),
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
				return this.answer.comments || []
			},
		},
		data() {
			return {
				loading: true,
			}
		}
	}
</script>
