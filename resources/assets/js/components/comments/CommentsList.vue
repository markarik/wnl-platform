<template>
	<div class="wnl-comments" ref="highlight">
		<div class="separate-controls">
			<div class="comments-controls">
				<span class="icon is-small comment-icon"><i class="fa fa-comments-o"></i></span>
				Komentarze ({{comments.length}})
				<span v-if="comments.length > 0 || this.showComments"> ·
					<a class="secondary-link" @click="toggleComments" v-text="toggleCommentsText"></a>
				</span> ·
				<span v-if="!readOnly">
					<a class="secondary-link" @click="toggleCommentsForm">Skomentuj</a>
				</span>
				<wnl-watch
				v-if="!hideWatchlist"
				:reactableId="commentableId"
				:reactableResource="commentableResource"
				:state="watchState"
				:reactionsDisabled="false"
				:module="module"
				/>
			</div>
			<slot/>
		</div>
		<wnl-comment
			v-if="showComments"
			v-for="comment in comments"
			:key="comment.id"
			:comment="comment"
			:profile="commentProfile(comment.profiles[0])"
			@removeComment="onRemoveComment"
			@resolveComment="onResolveComment"
			@unresolveComment="onUnresolveComment"
		/>
		<div class="form-container" v-if="showComments">
			<transition name="fade">
				<wnl-new-comment-form v-if="!readOnly"
					:commentableResource="commentableResource"
					:commentableId="commentableId"
					:isUnique="isUnique"
					@submitSuccess="onSubmitSuccess">
				</wnl-new-comment-form>
			</transition>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.separate-controls
		display: flex
		justify-content: space-between

	.comments-controls
		color: $color-gray-dimmed
		font-size: $font-size-minus-1
		margin-bottom: $margin-base
		margin-top: $margin-base

</style>

<script>
	import _ from 'lodash'
	import { mapGetters } from 'vuex'
	import { nextTick } from 'vue'

	import NewCommentForm from 'js/components/comments/NewCommentForm'
	import Comment from 'js/components/comments/Comment'
	import highlight from 'js/mixins/highlight'
	import Watch from 'js/components/global/reactions/Watch'


	import { scrollWithMargin } from 'js/utils/animations'

	export default {
		name: 'CommentsList',
		components: {
			'wnl-new-comment-form': NewCommentForm,
			'wnl-comment': Comment,
			'wnl-watch': Watch,
		},
		mixins: [highlight],
		props: [
			'module',
			'commentableResource',
			'commentableId',
			'isUnique',
			'urlParam',
			'hideWatchlist',
			'readOnly',
		],
		data() {
			return {
				formElement: {},
				listElement: {},
				showComments: false,
				highlightableResources: [this.urlParam, 'comment']
			}
		},
		computed: {
			...mapGetters(['currentUser', 'isOverlayVisible']),
			comments() {
				return this.getterFunction('comments', {
					resource: this.commentableResource,
					id: this.commentableId,
				})
			},
			toggleCommentsText() {
				return this.showComments ? this.$t('ui.action.hide') : this.$t('ui.action.show')
			},
			isCommentableInUrl() {
				return (_.get(this.$route, `query.${this.urlParam}`) == this.commentableId
					|| _.get(this.$route, 'query.commentable') == this.commentableId)
					&& (_.get(this.$route, 'query.comment'));
			},
			watchState() {
				return this.$store.getters[`${this.module}/getReaction`](
					this.commentableResource,
					this.commentableId,
					'watch'
				)
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
					nextTick(() => {
						this.commentsScroll(this.$el)
					})
				}
			},
			toggleCommentsForm() {
				this.showComments = true

				nextTick(() => {
					if (_.isUndefined(this.formElement)) {
						this.formElement = this.$el.getElementsByClassName('form-container')[0]
					}
					this.commentsScroll(this.formElement)
				})
			},
			commentsScroll(element) {
				if (_.isUndefined(element) || _.isNull(element)) return false;

				let parentOffset = element.offsetParent === null ? 0 : element.offsetParent.offsetTop
				scrollWithMargin(element.offsetTop + parentOffset)
			},
			onSubmitSuccess(data) {
				this.action('addComment', {
					commentableResource: this.commentableResource,
					commentableId: this.commentableId,
					comment: _.merge(data, { 'profiles': [ this.currentUser.id ] }),
					profile: this.currentUser,
				})

				nextTick(() => {
					let lastComment = _.last(this.$el.getElementsByClassName('wnl-comment')) || this.formElement
					this.commentsScroll(lastComment)
				})
			},
			onRemoveComment(id) {
				this.action('removeComment', {
					commentableResource: this.commentableResource,
					commentableId: this.commentableId,
					id,
				})
			},
			onResolveComment(id) {
				this.action('resolveComment', {
					commentableResource: this.commentableResource,
					commentableId: this.commentableId,
					id,
				})
			},
			onUnresolveComment(id) {
				this.action('unresolveComment', {
					commentableResource: this.commentableResource,
					commentableId: this.commentableId,
					id,
				})
			},
			refresh() {
				return this.action('setupComments', {
					resource: this.commentableResource,
					ids: [this.commentableId]
				})
			}
		},
		mounted() {
			this.formElement = this.$el.getElementsByClassName('form-container')[0]

			if (!this.isOverlayVisible && this.isCommentableInUrl) {
				this.scrollAndHighlight()
				this.showComments = true
			}
		},
		watch: {
			'comments' (newValue, oldValue) {
				if (newValue !== oldValue) {
					this.$emit('commentsUpdated', newValue)
				}
			},
			'isOverlayVisible' () {
				if (!this.isOverlayVisible && this.isCommentableInUrl) {
					this.scrollAndHighlight()
					this.showComments = true
				}
			},
			'showComments' (newValue) {
				let eventName = newValue ? 'commentsShown' : 'commentsHidden'
				this.$emit(eventName)
			},
			'$route' () {
				if (!this.isOverlayVisible && this.isCommentableInUrl) {
					this.refresh()
						.then(() => {
							this.scrollAndHighlight()
							this.showComments = true
						})
				}
			},
		},
	}
</script>
