<template>
	<div class="wnl-comments">
		<div class="comments-controls">
			<span class="icon is-small comment-icon"><i class="fa fa-comments-o"></i></span>
			Komentarze ({{comments.length}})
			<span v-if="comments.length > 0"> ·
				<a class="secondary-link" @click="toggleComments" v-text="toggleCommentsText"></a>
			</span> ·
			<span>
				<a class="secondary-link" @click="showCommentForm = !showCommentForm" v-text="toggleFormText"></a>
			</span>
		</div>
		<transition name="fade">
			<wnl-new-comment-form v-if="showCommentForm"
				:commentableResource="commentableResource"
				:commentableId="commentableId"
				@submitSuccess="onSubmitSuccess">
			</wnl-new-comment-form>
		</transition>
		<wnl-comment
			v-if="showComments"
			v-for="comment in comments"
			:key="comment.id"
			:comment="comment"
			:profile="commentProfile(comment.profiles[0])"
			@removeComment="onRemoveComment"
			>
			{{comment.text}}
		</wnl-comment>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.comments-controls
		color: $color-gray-dimmed
		font-size: $font-size-minus-1
		margin-bottom: $margin-base
		margin-top: $margin-base
</style>

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
				showCommentForm: false,
				showComments: false,
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
			toggleCommentsText() {
				return this.showComments ? 'Schowaj' : 'Pokaż'
			},
			toggleFormText() {
				return this.showCommentForm ? 'Ukryj' : 'Skomentuj'
			},
		},
		methods: {
			action(action, payload = {}) {
				return this.$store.dispatch(`${this.module}/${action}`, payload)
			},
			getter(getter) {
				return this.$store.getters[`${this.module}/${getter}`]
			},
			getterFunction(getter, payload) {
				return this.$store.getters[`${this.module}/${getter}`](payload)
			},
			commentProfile(id) {
				return this.getterFunction('commentProfile', id)
			},
			toggleComments() {
				this.showComments = !this.showComments
			},
			onSubmitSuccess(data) {
				this.action('addComment', {})
			},
			onRemoveComment(id) {
				this.action('removeComment', {
					commentable_resource: this.commentableResource,
					commentable_id: this.commentableId,
					id,
				})
			},
		},
	}
</script>
