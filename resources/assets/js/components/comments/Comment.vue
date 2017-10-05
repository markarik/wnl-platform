<template>
	<article class="wnl-comment media">
		<figure class="media-left">
			<p class="image is-32x32">
				<wnl-avatar size="medium" :fullName="profile.full_name"
							:url="profile.avatar">
				</wnl-avatar>
			</p>
		</figure>
		<div class="media-content">
			<div class="comment-content">
				<span class="author">{{profile.full_name}}</span>
				<div class="comment-text wrap" v-html="comment.text"></div>
				<small>{{time}}</small>
				<span v-if="isCurrentUserAuthor || $moderatorFeatures.isAllowed('access')">
					&nbsp;·
					<wnl-delete
						:requestRoute="requestRoute"
						:target="target"
						@deleteSuccess="onDeleteSuccess"
					></wnl-delete>
				</span>
				<wnl-resolve :resource="comment" @resolveResource="$emit('resolveComment', id)" @unresolveResource="$emit('unresolveComment', id)"/>
			</div>
		</div>
	</article>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.comment-content
		margin-top: -5px

	.author
		font-weight: $font-weight-bold

	.comment-text
		margin: $margin-small 0
		padding: 0

		p
			margin: 0

	.comment-icon-link
		.icon
			font-size: $font-size-minus-3
			vertical-align: middle
</style>

<script>
	import { mapGetters } from 'vuex'

	import Delete from 'js/components/global/form/Delete'
	import Resolve from 'js/components/global/form/Resolve'
	import { timeFromS } from 'js/utils/time'
	import moderatorFeatures from 'js/perimeters/moderator'

	export default {
		name: 'Comment',
		components: {
			'wnl-delete': Delete,
			'wnl-resolve': Resolve
		},
		perimeters: [moderatorFeatures],
		props: ['comment', 'profile'],
		computed: {
			...mapGetters(['currentUserId']),
			id() {
				return this.comment.id
			},
			time() {
				return timeFromS(this.comment.created_at)
			},
			requestRoute() {
				return `comments/${this.id}`
			},
			target() {
				return 'ten komentarz'
			},
			isCurrentUserAuthor() {
				return this.profile.id === this.currentUserId
			},
		},
		methods: {
			onDeleteSuccess() {
				this.$emit('removeComment', this.id)
			}
		}
	}
</script>
