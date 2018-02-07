<template lang="html">
	<router-link
		:to="to"
		:class="{'conversation-snippet': true, 'is-active-c': $route.query.roomId === room.id}">
		<figure class="media-left">

			<wnl-avatar
				:fullName="lastUser.display_name"
				:url="lastUser.avatar"
				size="large">
			</wnl-avatar>

		</figure>
		<div class="media-content">
			<div class="content">
				<div class="conversation-meta">
					<div class="conversation-names">
						<strong>{{ lastUser.display_name }}</strong>
					</div>
					<div class="conversation-time" v-if="room.last_message_time">
						<small>{{ time(room.last_message_time) }}</small>
					</div>
				</div>
				<div class="conversation-message" v-html="lastMessageContent"/>
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

		&.is-active-c
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
					query: {
						roomId: this.room.id,
					},
				}
			},
			lastMessageContent() {
				return this.messages.length ? this.messages[0].content : ''
			},
			lastUser() {
				return this.users.length ? this.users[0] : {}
			}
		},
		methods: {
			time(stamp){
				return shortTimeFromMs(stamp)
			}
		}
	}
</script>
