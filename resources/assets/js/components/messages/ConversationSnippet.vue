<template lang="html">
	<wnl-message-link
		:userId="profile.user_id"
		:roomId="roomId"
		class="conversation-snippet"
		:class="{'active-in-route': isActive}"
	>
		<figure class="media-left">

			<wnl-avatar
				:fullName="profile.display_name"
				:url="profile.avatar"
				size="large">
			</wnl-avatar>

		</figure>
		<div class="media-content">
			<div class="content">
				<div class="conversation-meta">
					<div class="conversation-names">
						<strong>{{ profile.display_name }}</strong>
					</div>
					<div class="conversation-time" v-if="room && room.last_message_time">
						<small>{{ time(room.last_message_time) }}</small>
					</div>
				</div>
				<div class="conversation-message" v-html="lastMessageContent"/>
			</div>
		</div>
	</wnl-message-link>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'

	.conversation-snippet
		display: flex
		min-width: 0
		overflow: hidden
		padding: $margin-medium
		cursor: pointer

		&:hover, &.active-in-route, &.active
			background-color: $color-background-lightest-gray

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
	import { mapGetters } from 'vuex'
	import MessageLink from "js/components/global/MessageLink";
	import {last} from 'lodash'

	export default {
		name: 'ConversationSnippet',
		components: {
			'wnl-message-link': MessageLink
		},
		props: {
			room: {
				required: false,
				default: () => ({})
			},
			profiles: {
				required: false,
				type: Array|Object,
				default: () => ([])
			},
			messages: {
				required: false,
				type: Array,
				default: () => ([])
			}
		},
		computed: {
			...mapGetters(['currentUserId']),
			lastMessageContent() {
				if (!this.messages || !this.messages.length) {
					return ''
				}

				return last(this.messages).content
			},
			isActive() {
				return this.$route.query.roomId === this.room.id
			},
			profile() {
				if (this.profiles instanceof Array) {
					if (this.profiles.length === 1) {
						return this.profiles[0]
					}

					return this.profiles.find(profile => profile.user_id !== this.currentUserId)
				}

				return this.profiles
			},
			roomId() {
				if (!this.room) {
					return 0;
				}

				return this.room.id
			}
		},
		methods: {
			time(stamp){
				return shortTimeFromMs(stamp)
			}
		}
	}
</script>
