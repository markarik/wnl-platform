<template>
	<div class="wnl-chat">
		<div :id="containerId" class="wnl-chat-messages">
			<div :id="contentId">
				<div v-if="loaded">
					<wnl-message v-for="(message, index) in messages"
						:showAuthor="isAuthorUnique[index]"
						:username="message.username"
						:time="message.time">
							{{ message.content }}
					</wnl-message>
				</div>
				<div v-else>
					Ładuję wiadomości...
				</div>
			</div>
		</div>
		<div class="wnl-chat-form">
			<wnl-message-form :loaded="loaded" :socket="socket" :room="room" :inputId="inputId"></wnl-message-form>
		</div>
	</div>
</template>
<style lang="sass">
	@import '../../../sass/variables'

	.wnl-chat
		display: flex
		flex: 1
		flex-direction: column
		justify-content: flex-end
		padding-right: 20px

	.wnl-chat-messages
		overflow-y: auto

	.wnl-chat-form
		border-top: $border-light-gray
		margin: 20px 0
		padding-top: 20px
</style>
<script>
	import Message from './Message.vue'
	import MessageForm from './MessageForm.vue'
	import * as socket from '../../socket'
	import { nextTick } from 'vue'

	export default {
		props: ['room'],
		data() {
			return {
				loaded: false,
				messages: [],
				socket: {}
			}
		},
		components: {
			'wnl-message': Message,
			'wnl-message-form': MessageForm
		},
		computed: {
			isAuthorUnique() {
				return this.messages.map((message, index) => {
					if (index === 0) return true

					let previous = index - 1
					return message.username !== this.messages[previous].username
				})
			},
			containerId() {
				return `wnl-chat-room-${this.room}`
			},
			container() {
				return document.getElementById(this.containerId)
			},
			contentId() {
				return `wnl-chat-content-${this.room}`
			},
			content() {
				return document.getElementById(this.contentId)
			},
			inputId() {
				return `wnl-chat-form-${this.room}`
			},
			input() {
				return document.getElementById(this.inputId)
			}
		},
		methods: {
			joinRoom() {
				this.socket.emit('join-room', {
					room: this.room
				})
				this.socket.on('join-room-success', (data) => {
					if (!this.loaded) {
						this.setListeners(this.socket)
						this.messages = data.messages
						this.loaded = true
						nextTick(() => {
							this.scrollToBottom()
						})
					}
				})
				return true
			},
			setListeners(socket) {
				socket.on('user-sent-message', (data) => {
					this.addMessage(data.message)
				})

				socket.on('message-processed', (data) => {
					if (data.sent) {
						this.addMessage(data.message)
					}
				})

				socket.on('error', (data) => {
					console.log(`Socket error: ${data}`)
				})
			},
			addMessage(message) {
				this.messages.push(message)
				nextTick(() => {
					this.scrollToBottom()
					this.input.focus()
				})
			},
			scrollToBottom() {
				this.container.scrollTop = this.content.offsetHeight
			}
		},
		mounted() {
			socket.connect().then((socket) => {
				this.socket = socket
				this.joinRoom()
			}).catch(console.log.bind(console))
		},
		beforeDestroy() {
			socket.disconnect().then(() => {
				return true
			}).catch(console.log.bind(console))
		}
	}

</script>
