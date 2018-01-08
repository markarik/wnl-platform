<template lang="html">
	<router-link
		:to="to"
		class="conversation-snippet">
		<figure class="media-left">

			<wnl-avatar
				:fullName="users[0].display_name"
				:url="users[0].avatar"
				size="large">

			</wnl-avatar>

		</figure>
		<div class="media-content">
			<div class="content">
				<div class="wnl-message-meta">
					<div class="names">
						<strong>{{ users[0].display_name }}</strong>
					</div>
					<div class="time">
						<small>{{ time(room.last_message_time) }}</small>
					</div>
				</div>
				<p class="wnl-message-content" v-html="message.content"></p>
			</div>
		</div>
	</router-link>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'

	.conversation-snippet
		display: flex
		padding: $margin-medium

		&:hover
			cursor: pointer
			background-color: $color-background-lightest-gray

		&.is-active
			background-color: $color-background-lighter-gray

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

				p
					margin: 0

</style>

<script>
	import { shortTimeFromMs } from 'js/utils/time'

	export default {
		name: 'ConversationSnippet',
		props: {
			room: {
				required: true,
			},
			currentRoom: {
				required: false,
			},
			users: {
				required: true,
				type: Array,
			}
		},
		data() {
			return {
				message: {
					content: '...',
				}
			}
		},
		computed: {
			to() {
				return {
					name: 'messages',
					params: {
						interlocutors: this.room.channel.replace('private-', ''),
					},
				}
			},
			isActive() {
				return this.room === this.currentRoom
			}
		},
		methods: {
			time(stamp){
				return shortTimeFromMs(stamp)
			}
		}
	}
</script>
