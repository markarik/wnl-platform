<template>
	<div class="wnl-comments">
		<wnl-comment v-for="comment in comments"
			:avatarUrl="comment.avatarUrl"
			:timestamp="comment.timestamp"
			:username="comment.username"
			:votes="comment.votes"
			>
			{{comment.text}}
		</wnl-comment>
	</div>
</template>

<script>
	import Comment from 'js/components/comments/Comment.vue'

	export default {
		name: 'CommentsList',
		components: {
			'wnl-comment': Comment,
		},
		props: ['comments']
	}
</script>
