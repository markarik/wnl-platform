<template lang="html">
	<div class="wnl-public-chat">
		<div class="chat-title">
			{{title || chatTitle}}
		</div>
		<div class="tabs">
			<ul>
				<li  v-for="room, key in rooms" :key="key" :class="{'is-active': isActive(room)}">
					<a @click="changeRoom(room)">{{ room.name }}</a>
				</li>
			</ul>
		</div>
		<a class="wnl-chat-close">
			<span v-if="canShowCloseIconInChat" class="icon wnl-chat-close" @click="toggleChat">
				<i class="fa fa-chevron-right"></i>
				<span>Ukryj czat</span>
			</span>
		</a>
		<wnl-chat
			:room="currentRoom"
			:messages="messages"
			:highlightedMessageId="highlightedMessageId"
			:hasMore="hasMore"
			:onScrollTop="pullMore"
			:loaded="loaded"
		/>
		<wnl-message-form
			:roomId="currentRoom.id"
			:room="currentRoom"
			:loaded="loaded"
			@messageSent="onMessageSent"
			@foundMentions="processMentions"
		></wnl-message-form>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-public-chat
		display: flex
		flex: 1
		flex-direction: column
		justify-content: space-between
		padding: $margin-base
		position: relative

		.wnl-chat-close
			color: $color-ocean-blue
			cursor: pointer
			display: flex
			flex-direction: column
			position: absolute
			right: $margin-base
			top: $margin-base

			span
				font-size: $font-size-minus-4
				text-transform: uppercase
				white-space: nowrap


	.metadata
		margin: $margin-base 0 0 $margin-base

	.chat-title
		color: $color-gray-dimmed
		font-size: $font-size-minus-2
		text-transform: uppercase

</style>

<script>
	import MessagesList from './MessagesList'
	import MessageForm from './MessageForm.vue'
	import { mapActions, mapGetters } from 'vuex'
	import _ from 'lodash'
	import {nextTick} from 'vue'
	import {
		SOCKET_EVENT_USER_SENT_MESSAGE,
		SOCKET_EVENT_MESSAGE_PROCESSED,
		SOCKET_EVENT_LEAVE_ROOM
	} from 'js/plugins/socket'


	export default {
		name: 'wnl-public-chat',
		components: {
			'wnl-chat': MessagesList,
			'wnl-message-form': MessageForm
		},
		props: ['title', 'rooms'],
		data () {
			return {
				currentRoom: this.getCurrentRoom(),
				loaded: false,
				highlightedMessageId: 0,
				messages: [],
				pagination: {
					has_more: false,
					next: null
				}
			}
		},
		computed: {
			...mapGetters([
				'canShowCloseIconInChat',
				'currentUserId',
				'currentUser'
			]),
			...mapGetters('course', ['getLesson']),
			...mapGetters('chatMessages', ['getRoomMessagesPagination']),
			chatTitle() {
				let lessonId = this.$route.params.lessonId

				if(typeof lessonId === 'undefined') {
					return 'Ogólny czat kursu'
				}

				return `Czat lekcji ${this.getLesson(lessonId).name}`
			},
			cursor() {
				return this.pagination.next
			},
			hasMore() {
				return this.pagination.has_more || false
			}
		},
		methods: {
			...mapActions(['toggleChat', 'saveMentions']),
			...mapActions('chatMessages', ['createPublicRoom', 'fetchRoomMessages']),
			changeRoom(room) {
				this.joinRoom(room.id)
				this.leaveRoom(this.currentRoom.id)
				this.currentRoom = room
			},
			isActive(room){
				return room.channel === this.currentRoom.channel
			},
			getCurrentRoom() {
				const query = this.$route.query

				if (query.chatChannel) {
					const room = _.find(this.rooms, room => room.channel === query.chatChannel)

					if (room) {
						return room
					} else {
						this.cleanupChatChannelParam()
						return this.rooms[0]
					}
				} else {
					return this.rooms[0]
				}
			},
			cleanupChatChannelParam() {
				const query = this.$route.query

				delete query.chatChannel

				this.$router.replace({
					...this.$route,
					query
				})
			},
			joinRoom() {
				const channel = this.$route.query.chatChannel

				if (channel && channel !== this.currentRoom.channel) {
					return this.changeRoom({
						channel,
						name: `#${_.last(channel.split('-'))}`
					})
				}

				this.createPublicRoom({slug: this.currentRoom.channel})
					.then(room => {
						this.currentRoom.id = room.id
						return this.fetchRoomMessages({room, limit: 50})
					})
					.then(messages => {
						this.messages = messages
						this.pagination = this.getRoomMessagesPagination(this.currentRoom.id)
						return this.$socketJoinRoom(this.currentRoom.id)
					})
					.then((data) => {
						if (!this.loaded) {
							nextTick(() => {
								const messageId = this.$route.query.messageId

								if (messageId && !this.isOverlayVisible) {
									this.highlightedMessageId = messageId
								}

								this.loaded = true
							})
						}
					})
			},
			setListeners() {
				this.$socketRegisterListener(SOCKET_EVENT_USER_SENT_MESSAGE, this.pushMessage)
			},
			removeListeners() {
				this.$socketRemoveListener(SOCKET_EVENT_USER_SENT_MESSAGE, this.pushMessage)
			},
			leaveRoom(roomId) {
				this.$socketEmit(SOCKET_EVENT_LEAVE_ROOM, {
					room: roomId
				})
			},
			pushMessage({message, room}) {
				if (this.currentRoom.id === room) {
					this.messages = [
						...this.messages,
						message
					]
				}
			},
			onMessageSent({sent, ...data}) {
				if (sent) {
					this.pushMessage(data)
				}
			},
			pullMore() {
				return this.fetchRoomMessages({room: this.currentRoom, currentCursor: this.cursor, limit: 50})
					.then(messages => {
						this.messages = messages.concat(this.messages)
						this.pagination = this.getRoomMessagesPagination(this.currentRoom.id)
					}).catch(error => $wnl.logger.capture(error))
			},
			processMentions({mentions, context}) {
				this.saveMentions(this.getMentionsData(mentions, context))
			},
			getMentionsData(userIds, message) {
				return {
					mentioned_users: userIds,
					subject: {
						type: 'chat_message',
						id: `${message.time}${this.currentUserId}`,
						text: message.content,
						channel: this.currentRoom.channel
					},
					objects: {
						type: "chat_channel",
						text: this.currentRoom.name
					},
					context: {
						name: this.$route.name,
						params: this.$route.params
					},
					actors: this.currentUser
				}
			}
		},
		mounted() {
			this.joinRoom()
			this.setListeners()
		},
		beforeDestroy() {
			this.leaveRoom(this.currentRoom.id)
			this.removeListeners()
		},
		watch: {
			'rooms' (newValue, oldValue) {
				if (newValue.length === oldValue.length) return
				this.changeRoom(newValue[0])
			}
		}
	}
</script>
