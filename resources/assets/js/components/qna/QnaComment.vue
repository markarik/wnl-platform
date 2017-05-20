<template>
	<div class="qna-comment">
		<div class="qna-container">
			<div class="qna-meta qna-comment-meta">
				<wnl-avatar
				:username="comment.author.username"
				:url="comment.author.avatarUrl"
				size="small">
				</wnl-avatar>
				<span class="qna-meta-info">
					{{comment.author.username}} ·
				</span>
				<span class="qna-meta-info">
					{{comment.timestamp}}
				</span>
			</div>
			<div class="qna-comment-content">
				{{comment.text}}
			</div>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.qna-comment
		border-top: $border-light-gray
		font-size: $font-size-minus-1
		margin-bottom: $margin-base
		padding-top: $margin-base

		&:first-child
			border: 0

	.qna-comment-meta
		font-size: $font-size-minus-2
		margin-top: 0
		margin-bottom: $margin-small
</style>

<script>
	export default {
		name: 'QnaComment',
		props: ['comment'],
	}
</script>
