<template>
	<div class="qna-comment">
		<div class="qna-container">
			<div class="qna-comment-content" v-html="comment.text"></div>
			<div class="qna-meta qna-comment-meta">
				<wnl-avatar
						:fullName="author.full_name"
						:url="author.avatar"
						size="small">
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

	.qna-comment-content
		word-wrap: break-word
		word-break: break-word

	.qna-comment-meta
		font-size: $font-size-minus-2
		margin-top: 0
		margin-bottom: $margin-small
</style>

<script>
	import { mapGetters, mapActions } from 'vuex'

	import Delete from 'js/components/global/form/Delete'
	import { timeFromS } from 'js/utils/time'

	export default {
		name: 'QnaComment',
		components: {
			'wnl-delete': Delete,
		},
		props: ['comment', 'answerId', 'readOnly'],
		computed: {
			...mapGetters('qna', [
				'profile'
			]),
			...mapGetters(['currentUserId']),
			id() {
				return this.comment.id
			},
			author() {
				return this.profile(this.comment.profiles[0])
			},
			time() {
				return timeFromS(this.comment.created_at)
			},
			isCurrentUserAuthor() {
				return this.author.id === this.currentUserId
			},
			deleteTarget() {
				return 'ten komentarz'
			},
			resourceRoute() {
				return `comments/${this.id}`
			}
		},
		methods: {
			...mapActions('qna', ['removeComment']),
			onDeleteSuccess() {
				this.removeComment({
					answerId: this.answerId,
					commentId: this.id,
				})
			},
		},
	}
</script>
