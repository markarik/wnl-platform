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
				<div class="conversation-meta">
					<div class="conversation-names">
						<strong>{{ users[0].display_name }}</strong>
					</div>
					<div class="conversation-time" v-if="room.last_message_time">
						<small>{{ time(room.last_message_time) }}</small>
					</div>
				</div>
				<div class="conversation-message" v-html="messages[0].content">
				</div>
			</div>
		</div>
	</router-link>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'

	.conversation-snippet
		display: flex
		min-width: 0
		overflow: hidden
		padding: $margin-medium

		&:hover
			cursor: pointer
			background-color: $color-background-lightest-gray

		&.is-active
			background-color: $color-background-lighter-gray


		.media-content
			min-width: 0
			overflow: hidden

			.conversation-meta
				display: flex
				overflow: hidden
				justify-content: space-between

				.conversation-names
					min-width: 0
					flex: 1 1 0%
					overflow: hidden
					white-space: nowrap
					text-overflow: ellipsis

				.conversation-time
					display: flex
					min-width: 0

			.conversation-message
				min-width: 0
				flex: 1 1 0%
				overflow: hidden
				white-space: nowrap
				text-overflow: ellipsis

</style>

<script>
	import { shortTimeFromMs } from 'js/utils/time'

	export default {
		name: 'ConversationSnippet',
		props: {
			room: {
				required: true,
			},
			users: {
				required: true,
				type: Array,
			},
			messages: {
				required: true,
				type: Array
			}
		},
		data() {
			return {

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
			}
		},
		methods: {
			time(stamp){
				return shortTimeFromMs(stamp)
			}
		},
		mounted () {
			console.log('conversation snippet created...')
		}
	}
</script>
