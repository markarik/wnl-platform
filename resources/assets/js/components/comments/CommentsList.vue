<template>
	<div class="wnl-comments">
		<wnl-new-comment-form v-if="showCommentForm"
			:commentableResource="commentableResource"
			:commentableId="commentableId"
			@submitSuccess="onSubmitSuccess">
		</wnl-new-comment-form>
		<wnl-comment v-for="comment in comments"
			:key="comment.id"
			:comment="comment"
			:profile="commentProfile(comment.profiles[0])"
			@removeComment="onRemoveComment"
			>
			{{comment.text}}
		</wnl-comment>
		<!-- <div v-else>
			Ni mo komentorzy
		</div> -->
	</div>
</template>

<script>
	import _ from 'lodash'
	import { mapGetters, mapActions } from 'vuex'

	import NewCommentForm from 'js/components/comments/NewCommentForm'
	import Comment from 'js/components/comments/Comment'

	export default {
		name: 'CommentsList',
		components: {
			'wnl-new-comment-form': NewCommentForm,
			'wnl-comment': Comment,
		},
		props: ['module', 'commentableResource', 'commentableId'],
		data() {
			return {
				showCommentForm: true,
			}
		},
		computed: {
			comments() {
				return this.getterFunction('comments', {
					resource: this.commentableResource,
					id: this.commentableId,
				})
			},
			hasComments() {
				return !_.isEmpty(this.comments)
			},
		},
		methods: {
			commentProfile(id) {
				return this.getterFunction('commentProfile', id)
			},
			action(action, payload = {}) {
				return this.$store.dispatch(`${this.module}/${action}`, payload)
			},
			getter(getter) {
				return this.$store.getters[`${this.module}/${getter}`]
			},
			getterFunction(getter, payload) {
				return this.$store.getters[`${this.module}/${getter}`](payload)
			},
			// ...mapActions(this.module, ['addComment', 'removeComment']),
			onSubmitSuccess(data) {
				this.action('addComment', {})
			},
			onRemoveComment(id) {
				this.action('removeComment', {})
			},
		}
	}
</script>
