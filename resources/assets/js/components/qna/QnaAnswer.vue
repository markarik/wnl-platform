<template>
	<div>
		<div class="qna-answer">
			<div class="votes">
				<wnl-vote type="up" :count="answer.votes"></wnl-vote>
			</div>
			<div class="qna-container">
				<div class="qna-answer-content" v-html="answer.text"></div>
				<div class="qna-meta">
					<wnl-avatar
						:username="answer.author.username"
						:url="answer.author.avatarUrl"
						size="medium">
					</wnl-avatar>
					<span class="qna-meta-info">
						{{answer.author.username}} ·
					</span>
					<span class="qna-meta-info">
						{{answer.timestamp}}
					</span>
				</div>
			</div>
		</div>
		<div class="qna-answer-comments">
			<p class="qna-title">Komentarze ({{answer.comments.length}})</p>
			<wnl-qna-comment v-for="comment in answer.comments" :comment="comment"></wnl-qna-comment>
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
	import Vote from 'js/components/qna/Vote'
	import QnaComment from 'js/components/qna/QnaComment'

	export default {
		name: 'QnaAnswer',
		props: ['answer'],
		components: {
			'wnl-vote': Vote,
			'wnl-qna-comment': QnaComment,
		},
	}
</script>
