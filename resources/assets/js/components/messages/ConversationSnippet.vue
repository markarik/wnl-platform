<template lang="html">
	<div class="conversation-snippet">
		<figure class="media-left">

			<wnl-avatar
				:fullName="users[0].full_name"
				:url="users[0].avatar"
				size="large">

			</wnl-avatar>

		</figure>
		<div class="media-content">
			<div class="content">
				<div class="wnl-message-meta">
					<div class="names">
						<strong>{{ users[0].fullName }}, Ktos tam</strong>
					</div>
					<div class="time">
						<small>{{ message.time }}</small>
					</div>
				</div>
				<p class="wnl-message-content" v-html="message.content"></p>
			</div>
		</div>
	</div>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'

	.conversation-snippet
		display: flex
		padding: $margin-medium

		&:hover
			cursor: pointer
			background-color: $color-background-lightest-gray

		.media-content
			.content
				color: $color-gray-lighter
				word-wrap: break-word
				word-break: break-word

				.wnl-message-meta
					color: $color-inactive-gray
					line-height: 1em
					margin-bottom: $margin-tiny
					display: flex
					justify-content: space-between

					.names
						overflow: hidden
						white-space: nowrap
						text-overflow: ellipsis
						display: flex
						flex: 1 1 0%
						
					.time
						display: inline-block
						padding: 0 $margin-small 0 $margin-small

				p
					margin: 0

</style>

<script>
	import { timeFromMs } from 'js/utils/time'

	export default {
		name: 'ConversationSnippet',
		props: {
			room: {
				required: true,
			},
			users: {
				required: true,
				type: Array,
			}
		},
		data() {
			return {
//				users: [
//					{
//						fullName: 'Adam Karmiński',
//						avatar: 'https://api.adorable.io/avatars/50/' + Math.random()
//					}
//				],
				message: {
					content: 'no siema',
					time: timeFromMs(1509983162118)
				}
			}
		},
	}
</script>
