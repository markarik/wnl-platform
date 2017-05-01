<template>
	<article class="wnl-comment media">
		<figure class="media-left">
			<p class="image is-32x32">
				<wnl-avatar size="medium" :username="username"
					:url="avatarUrl">
				</wnl-avatar>
			</p>
		</figure>
		<div class="media-content">
			<div class="content">
				<strong>{{username}}</strong>
				<p class="comment-text">
					<slot></slot>
				</p>
				<small>
					<a class="comment-icon-link" @click="mockSaving">
						<span class="icon is-small"><i class="fa fa-star-o"></i></span> {{votes}}
					</a> · {{timestamp}}
				</small>
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
	import { swalConfig } from 'js/utils/swal'

	export default {
		name: 'Comment',
		props: ['avatarUrl', 'timestamp', 'username', 'votes'],
		methods: {
			/**
			 * A temporary method, to be removed when Collections are done.
			 * The method displays a modal informing about the upcoming feature.
			 */
			mockSaving() {
				this.$swal(swalConfig({
					html: `<p class="normal">Nad ocenianiem komentarzy też już pracujemy!</p>`,
					title: 'Już wkrótce!',
					type: 'info',
				}))
			}
		}
	}
</script>
