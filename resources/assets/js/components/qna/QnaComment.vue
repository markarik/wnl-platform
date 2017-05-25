<template>
	<div class="qna-comment">
		<div class="qna-container">
			<div class="qna-meta qna-comment-meta">
				<wnl-avatar
				:username="author.username"
				:url="author.avatarUrl"
				size="small">
				</wnl-avatar>
				<span class="qna-meta-info">
					{{author.username}} ·
				</span>
				<span class="qna-meta-info">
					{{time}}
				</span>
			</div>
			<div class="qna-comment-content" v-html="comment.text"></div>
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
	import { mapGetters } from 'vuex'

	import { timeFromS } from 'js/utils/time'

	export default {
		name: 'QnaComment',
		props: ['comment'],
		computed: {
			...mapGetters('qna', [
				'profile'
			]),
			id() {
				return this.comment.id
			},
			author() {
				return this.profile(this.comment.profiles[0])
			},
			time() {
				return timeFromS(this.comment.created_at)
			},
		},
	}
</script>
