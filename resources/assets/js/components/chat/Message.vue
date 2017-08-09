<template>
	<article class="media wnl-chat-message" :class="{ 'is-full': showAuthor }">
		<figure class="media-left">
			<wnl-avatar :fullName="fullName" :url="avatar" v-if="showAuthor"></wnl-avatar>
			<div class="media-left-placeholder" v-else></div>
		</figure>
		<div class="media-content">
			<div class="content">
				<p class="wnl-message-meta" v-if="showAuthor">
					<strong>{{ fullName }}</strong>
					<small class="wnl-message-time">{{ formattedTime }}</small>
				</p>
				<p class="wnl-message-content" v-html="content"></p>
			</div>
		</div>
	</article>
</template>
<style lang="sass">
	@import 'resources/assets/sass/variables'

	.media.wnl-chat-message
		border-top: 0
		padding-top: 0
		margin-top: 0.5rem

		&.is-full
			margin-top: 1.25rem

		.media-left
			margin: 0 $margin-small 0 0

		.media-left-placeholder
			height: 1px
			width: map-get($rounded-square-sizes, 'medium')

		.media-content
			.content
				color: $color-gray-lighter
				word-wrap: break-word
				word-break: break-word

				.wnl-message-meta
					color: $color-inactive-gray
					line-height: 1em
					margin-bottom: $margin-tiny

				.wnl-message-time
					margin-left: $margin-small

				p
					margin: 0
</style>
<script>
	import { timeFromMs } from 'js/utils/time'

	export default{
		props: ['fullName', 'avatar', 'time', 'showAuthor', 'content'],
		computed: {
			formattedTime () {
				return timeFromMs(this.time)
			}
		}
	}
</script>
