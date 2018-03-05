<template>
	<div class="wnl-chat">
		<div class="wnl-chat-messages" @scroll="onScroll"
			 ref="messagesContainer">
			<div class="wnl-chat-content">
				<div class="wnl-chat-content-inside" v-if="loaded">
					<div class="notification aligncenter" v-if="!thereIsMore">
						To początek dyskusji na tym kanale!
					</div>
					<wnl-text-loader v-if="isPulling">
						Ładuję wiadomości...
					</wnl-text-loader>
					<div v-if="messages.length > 0">
						<wnl-message v-for="(message, index) in messages"
									 :key="index"
									 :showAuthor="isAuthorUnique[index]"
									 :id="getMessageId(message)"
									 :author="getMessageAuthor(message)"
									 :fullName="getMessageAuthor(message).full_name"
									 :displayName="getMessageAuthor(message).display_name"
									 :avatar="getMessageAuthor(message).avatar"
									 :time="message.time"
									 :content="message.content"
						></wnl-message>
					</div>
					<div class="metadata aligncenter margin vertical" v-else>
						Napisz pierwszą wiadomość i zacznij rozmowę!
						<p class="margin vertical">
							<span class="icon is-big text-dimmed">
								<i class="fa fa-comments-o"></i>
							</span>
						</p>
					</div>
				</div>
				<wnl-text-loader v-else>Ładuję wiadomości...</wnl-text-loader>
			</div>
		</div>
		<div class="wnl-chat-form">
			<wnl-message-form
					:loaded="loaded"
					:room="room"
					ref="messageForm"
			></wnl-message-form>
		</div>
	</div>
</template>
<style lang="sass" rel="stylesheet/sass">
	@import '../../../sass/variables'

	.wnl-chat
		display: flex
		flex: 1
		flex-direction: column
		justify-content: space-between

	.wnl-chat-messages
		display: flex
		flex: 1 1 0
		flex-direction: column-reverse
		overflow-y: auto

	.wnl-chat-content
		position: relative

	.wnl-chat-form
		border-top: $border-light-gray
		margin: $margin-base 0 0
		padding-top: $margin-base
</style>
<script>
	import Message from './Message.vue'
	import MessageForm from './MessageForm.vue'
	import {getApiUrl} from 'js/utils/env'
	import {nextTick} from 'vue'
	import _ from 'lodash'
	import highlight from 'js/mixins/highlight'
	import {
		SOCKET_EVENT_USER_SENT_MESSAGE,
		SOCKET_EVENT_MESSAGE_PROCESSED,
		SOCKET_EVENT_LEAVE_ROOM
	} from 'js/plugins/socket'

	import {mapGetters, mapActions} from 'vuex'

	export default {
		props: {
			room: {
				required: true,
			},
			switchRoom: {
				required: true,
				type: Function
			},
			pullLimit: {
				required: false,
				type: Number,
				default: 100
			},
			initialPull: {
				required: false,
				type: Boolean,
				default: false,
			}
		},
		data() {
			return {
				loaded: false,
				messages: [],
				isPulling: false,
				thereIsMore: true,
			}
		},
		mixins: [highlight],
		computed: {
			...mapGetters(['isOverlayVisible']),
			...mapGetters('chatMessages', ['getRoomById', 'getProfileByUserId']),
			isAuthorUnique() {
				return this.messages.map((message, index) => {
					if (index === 0) return true

					let previous     = index - 1,
						halfHourInMs = 1000 * 60 * 30

					return message.user_id !== this.messages[previous].user_id ||
						message.time - this.messages[previous].time > halfHourInMs
				})
			},
			container() {
				return this.$el.getElementsByClassName('wnl-chat-messages')[0]
			},
			content() {
				return this.$el.getElementsByClassName('wnl-chat-content')[0]
			},
			contentInside() {
				// In case you wonder - thank Firefox :/
				return this.$el.getElementsByClassName('wnl-chat-content-inside')[0]
			},
		},
		methods: {
			...mapActions('chatMessages', ['createPublicRoom', 'initPublicRoom']),
			joinRoom() {
				const channel = this.$route.query.chatChannel

				if (channel && channel !== this.room.channel) {
					return this.switchRoom({
						channel,
						name: `#${_.last(channel.split('-'))}`
					})
				}

				this.createPublicRoom({name: this.room.channel})
					.then(room => {
						this.room.id = room.id
						return this.initPublicRoom(room)
					})
					.then(room => {
						const {messages} = this.getRoomById(room.id)
						this.messages = messages

						return this.$socketJoinRoom(room.id)
					})
					.then((data) => {
						if (!this.loaded) {
							this.loaded = true

							nextTick(() => {
								const messageId = this.$route.query.messageId

								if (messageId && !this.isOverlayVisible) {
									this.scrollToMessageById(messageId)
								} else {
									this.scrollToBottom()
								}
							})
						}
					})
			},
			leaveRoom(roomId) {
				this.$socketEmit(SOCKET_EVENT_LEAVE_ROOM, {
					room: roomId
				})
			},
			changeRoom(oldRoom) {
				this.loaded = false
				this.$socketEmit(SOCKET_EVENT_LEAVE_ROOM, {
					room: oldRoom.channel
				})
				this.joinRoom()
			},
			setListeners() {
				this.$socketRegisterListener(SOCKET_EVENT_USER_SENT_MESSAGE, this.pushMessage)
				this.$socketRegisterListener(SOCKET_EVENT_MESSAGE_PROCESSED, this.addMessage)
			},
			removeListeners() {
				this.$socketRemoveListener(SOCKET_EVENT_USER_SENT_MESSAGE, this.pushMessage)
				this.$socketRemoveListener(SOCKET_EVENT_MESSAGE_PROCESSED, this.addMessage)
			},
			pushMessage({message, room}) {
				if (this.room.channel === room) {
					this.messages.push(message)
				}
			},
			addMessage(data) {
				if (data.sent) {
					this.pushMessage(data)
					nextTick(() => {
						this.scrollToBottom()
						this.$refs.messageForm.quillEditor.quill.focus()
					})
				}
			},
			scrollToBottom() {
				this.container.scrollTop = '1000000000'
			},
			scrollToTop() {
				this.container.scrollTop = '0'
			},
			pullDebouncer(event) {
				let target     = event.target,
					height     = target.scrollHeight,
					shouldPull =
						// make sure we're not pulling from cold storage at the moment,
						!this.isPulling &&
						// we're reaching the top of the messages container,
						(target.scrollTop / height) < 0.1 &&
						// cold storage has some more messages we can pull.
						this.thereIsMore

				if (shouldPull) this.pull(height)
			},
			onScroll(event) {
				this.pullDebouncer.call(this, event)
			},
			pull(originalHeight) {
				this.isPulling = true
				let data       = {
					query: {},
					order: {
						time: 'desc',
					},
					include: 'profiles',
					limit: [this.pullLimit, 0]
				}

				if (this.messages.length > 0) {
					data.query.where = [
						['time', '<', this.messages[0].time],
					];
				}

				axios.post(getApiUrl(`chat_rooms/${this.room}/chat_messages/.search`), data)
					.then(response => {
						let data = response.data

						if (typeof data[0] !== 'object') {
							this.isPulling   = false
							this.thereIsMore = false
							return
						}

						let profiles = data.included.profiles

						_.each(data, (element, key) => {
							if (key !== 'included') {
								let user    = element.profiles[0],
									message = _.assign(element, profiles[user])
								this.messages.unshift(message);
							}
						})
						setTimeout(() => {
							const messageId = this.$route.query.messageId
							const channel   = this.$route.query.chatChannel

							if (channel && channel !== this.room.channel) {
								this.switchRoom({
									channel,
									name: `#${_.last(channel.split('-'))}`
								})
							} else if (messageId && !this.isOverlayVisible) {
								this.scrollToMessageById(messageId)
							} else {
								this.container.scrollTop = this.container.scrollHeight - originalHeight
							}
						}, 0)
						this.isPulling = false
					})
					.catch(error => {
						$wnl.logger.capture(error)
					})
			},
			scrollToMessageById(id) {
				const matchingMessage = this.$el.querySelector(`[data-id="${id}"]`)

				if (matchingMessage) {
					this.$refs.highlight = matchingMessage
					this.scrollToPositionAndHighlight(
						["chatChannel", "messageId"],
						matchingMessage.offsetTop,
						this.$refs.messagesContainer
					)
				} else {
					this.scrollToTop()
				}
			},
			getMessageId(message) {
				return `${message.time}${message.user && message.user.id}`
			},
			getMessageAuthor(message) {
				return this.getProfileByUserId(message.user_id)
			}
		},
		mounted() {
			this.joinRoom()
			this.setListeners()

			this.pullDebouncer = _.debounce(this.pullDebouncer, 50)
			if (this.initialPull) {
				this.pull(0)
			}
		},
		beforeDestroy() {
			this.leaveRoom(this.room.channel)
			this.removeListeners()
		},
		components: {
			'wnl-message': Message,
			'wnl-message-form': MessageForm,
		},
		watch: {
			'room'(newRoom, oldRoom) {
				if (newRoom !== oldRoom) {
					this.changeRoom(oldRoom)
				}
			},
			'$route'(newRoute, oldRoute) {
				const messageId = this.$route.query.messageId
				const channel   = this.$route.query.chatChannel

				if (channel && channel !== this.room.channel) {
					this.switchRoom({
						channel,
						name: `#${_.last(channel.split('-'))}`
					})
				} else if (messageId && !this.isOverlayVisible) {
					this.scrollToMessageById(messageId)
				}
			},
		}
	}

</script>
