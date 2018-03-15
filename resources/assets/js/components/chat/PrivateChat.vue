<template lang="html">
	<div class="wnl-private-chat">
		<div class="chat-title">
			<wnl-avatar
				:fullName="interlocutorProfile.full_name"
				:url="interlocutorProfile.avatar"
				size="small"
				class="chat-title__avatar"
			/>
			<span class="chat-title__name">{{chatTitle}}</span>
		</div>
		<wnl-chat
			:room="room"
			:messages="room.messages"
			:hasMore="hasMore"
			:onScrollTop="pullMore"
			:loaded="true"
		/>
		<wnl-message-form
			:room="room"
			:messagePayload="{users}"
			:autofocusOnRoomChange="true"
			@messageSent="onMessageSent"
		/>
	</div>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'

	.wnl-private-chat
		display: flex
		flex: 1
		flex-direction: column
		justify-content: space-between
		position: relative
		width: 100%

	.chat-title
		display: flex
		flex-direction: row
		align-items: center
		text-align: center
		justify-content: center
		border-bottom: $border-light-gray
		padding-bottom: $margin-base

		&__name
			margin-left: $margin-small
			padding-top: $margin-tiny

</style>

<script>
	import {SOCKET_EVENT_USER_SENT_MESSAGE} from 'js/plugins/socket'
	import MessageForm from './MessageForm.vue'
	import MessagesList from './MessagesList.vue'
	import {getApiUrl} from 'js/utils/env'

	import { mapGetters, mapActions } from 'vuex'

	export default {
		PRIVATE_CHAT_MESSAGES_LIMIT: 50,
		components: {
			'wnl-message-form': MessageForm,
			'wnl-chat': MessagesList
		},
		props: {
			room: {
				type: Object,
				required: true
			},
			users: {
				type: Array,
				required: true
			}
		},
		data() {
			return {
				pagination: {
					// 50 is original limit -> better way to handle that
					has_more: this.room.messages.length === this.PRIVATE_CHAT_MESSAGES_LIMIT,
					next: (_.first(this.room.messages) || {}).time
				}
			}
		},
		computed: {
			...mapGetters(['isOverlayVisible', 'currentUserId', 'currentUserDisplayName']),
			...mapGetters('chatMessages', ['getProfileByUserId', 'profiles', 'getInterlocutor', 'getRoomMessagesPagination']),
			interlocutorProfile() {
				return this.getInterlocutor(this.room.profiles)
			},
			chatTitle() {
				return this.interlocutorProfile.display_name || this.currentUserDisplayName
			},
			hasMore() {
				return this.pagination.has_more || false
			},
			cursor() {
				return this.pagination.next || null
			}
		},
		methods: {
			...mapActions('chatMessages', ['markRoomAsRead', 'onNewMessage', 'fetchRoomMessages']),
			getMessageAuthor(message) {
				return this.getProfileByUserId(message.user_id)
			},
			onMessageSent({sent, ...data}) {
				this.onNewMessage(data)
			},
			pullMore() {
				return this.fetchRoomMessages({room: this.room, currentCursor: this.cursor, limit: this.PRIVATE_CHAT_MESSAGES_LIMIT, append: true})
					.then(messages => {
						this.pagination = this.getRoomMessagesPagination(this.room.id)
					}).catch(error => $wnl.logger.capture(error))
			},
			markAsRead({room}) {
				if (room === this.room.id) {
					this.$socketMarkRoomAsRead(this.room.id)
						.then(() => this.markRoomAsRead(this.room.id))
						.catch(err => $wnl.logger.error(err))
				}
			},
			addEventListeners() {
				this.$socketRegisterListener(SOCKET_EVENT_USER_SENT_MESSAGE, this.markAsRead)
			},
			removeEventListeners() {
				this.$socketRemoveListener(SOCKET_EVENT_USER_SENT_MESSAGE, this.markAsRead)
			}
		},
		mounted() {
			this.addEventListeners()
		},
		beforeDestroy() {
			this.removeEventListeners()
		}
	}

</script>
