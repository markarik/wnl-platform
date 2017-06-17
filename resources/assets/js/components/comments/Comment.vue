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
			<div class="content">
				<strong>{{profile.full_name}}</strong>
				<div class="comment-text" v-html="comment.text"></div>
				<small>{{time}}</small>
				<span v-if="isCurrentUserAuthor">
					&nbsp;·
					<wnl-delete
						:requestRoute="requestRoute"
						:target="target"
						@deleteSuccess="onDeleteSuccess"
					></wnl-delete>
				</span>
			</div>
		</div>
	</article>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.content
		margin-top: -5px

	p.comment-text
		margin: $margin-small 0
		padding: 0

	.comment-icon-link
		.icon
			font-size: $font-size-minus-3
			vertical-align: middle
</style>

<script>
	import { mapGetters } from 'vuex'

	import Delete from 'js/components/global/form/Delete'
	import { timeFromS } from 'js/utils/time'

	export default {
		name: 'Comment',
		components: {
			'wnl-delete': Delete,
		},
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
