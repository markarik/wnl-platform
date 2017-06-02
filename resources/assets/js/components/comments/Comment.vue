<template>
	<article class="wnl-comment media">
		<figure class="media-left">
			<p class="image is-32x32">
				<wnl-avatar size="medium" :username="profile.full_name"
					:url="profile.avatar">
				</wnl-avatar>
			</p>
		</figure>
		<div class="media-content">
			<div class="content">
				<strong>{{profile.full_name}}</strong>
				<div class="comment-text" v-html="comment.text"></div>
				<small>{{time}}</small>
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
	import { mapActions } from 'vuex'

	import { timeFromS } from 'js/utils/time'

	export default {
		name: 'Comment',
		props: ['comment', 'profile'],
		computed: {
			time() {
				return timeFromS(this.comment.created_at)
			},
		},
		methods: {
			onClickDelete() {
				this.$emit('removeComment', this.id)
			}
		}
	}
</script>
