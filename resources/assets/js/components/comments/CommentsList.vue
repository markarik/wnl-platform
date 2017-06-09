<template>
	<div class="wnl-comments">
		<div class="comments-controls">
			<span class="icon is-small comment-icon"><i class="fa fa-comments-o"></i></span>
			Komentarze ({{comments.length}})
			<span v-if="comments.length > 0"> ·
				<a class="secondary-link" @click="toggleComments" v-text="toggleCommentsText"></a>
			</span> ·
			<span>
				<a class="secondary-link" @click="toggleCommentsForm">Skomentuj</a>
			</span>
		</div>
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
		<div class="form-container">
			<transition name="fade">
				<wnl-new-comment-form v-if="showComments"
					:commentableResource="commentableResource"
					:commentableId="commentableId"
					@submitSuccess="onSubmitSuccess">
				</wnl-new-comment-form>
			</transition>
		</div>
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
	import { nextTick } from 'vue'

	import NewCommentForm from 'js/components/comments/NewCommentForm'
	import Comment from 'js/components/comments/Comment'

	import { scrollWithMargin } from 'js/utils/animations'

	export default {
		name: 'CommentsList',
		components: {
			'wnl-new-comment-form': NewCommentForm,
			'wnl-comment': Comment,
		},
		props: ['module', 'commentableResource', 'commentableId'],
		data() {
			return {
				showComments: false,
				listElement: {},
				formElement: {},
			}
		},
		computed: {
			...mapGetters(['currentUser']),
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

				if (this.showComments) {
					this.commentsScroll(this.$el)
				}
			},
			toggleCommentsForm() {
				this.showComments = true

				nextTick(() => {
					this.commentsScroll(this.formElement)
				})
			},
			commentsScroll(element) {
				scrollWithMargin(element.offsetTop + element.offsetParent.offsetTop)
			},
			onSubmitSuccess(data) {
				this.action('addComment', {
					commentableResource: this.commentableResource,
					commentableId: this.commentableId,
					comment: _.merge(data, { 'profiles': [ this.currentUser.id ] }),
					profile: this.currentUser,
				})

				let lastComment = _.last(this.$el.getElementsByClassName('wnl-comment'))
				this.commentsScroll(lastComment)
			},
			onRemoveComment(id) {
				this.action('removeComment', {
					commentableResource: this.commentableResource,
					commentableId: this.commentableId,
					id,
				})
			},
		},
		mounted() {
			this.formElement = this.$el.getElementsByClassName('form-container')[0]
		}
	}
</script>
