<template lang="html">
	<div class="wnl-private-chat">
		<div class="chat-title">
			<wnl-avatar :fullName="interlocutorProfile.full_name" :url="interlocutorProfile.avatar"/>
			<span>{{chatTitle}}</span>
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
		flex-direction: column
		align-items: center
		text-align: center
		border-bottom: $border-light-gray
		margin: $margin-base 0 0
		padding-bottom: $margin-base

</style>

<script>
	import MessageForm from './MessageForm.vue'
	import MessagesList from './MessagesList.vue'
	import {getApiUrl} from 'js/utils/env'

	import { mapGetters, mapActions } from 'vuex'

	export default {
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
					has_more: this.room.messages.length === 50,
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
				return this.fetchRoomMessages({room: this.room, currentCursor: this.cursor, limit: 50})
					.then(messages => {
						this.pagination = this.getRoomMessagesPagination(this.room.id)
					}).catch(error => $wnl.logger.capture(error))
			}
		},
		watch: {
			'room.messages.length' (newValue, oldValue) {
				if (newValue > oldValue) {
					const newMessages = this.room.messages.slice(oldValue, newValue)
					if (newMessages.find(msg => msg.user_id !== this.currentUserId)) {
						this.$socketMarkRoomAsRead(this.room.id)
							.then(() => this.markRoomAsRead(this.room.id))
							.catch(err => $wnl.logger.capture(err))
					}
				}
			}
		},
	}

</script>
