<template>
	<div>
		<div :id="containerId" class="wnl-chat-messages">
			<div :id="contentId">
				<div v-if="loaded">
					<wnl-message v-for="message in messages" :username="message.username" :timeago="message.timeago">
						{{ message.content }}
					</wnl-message>
				</div>
				<div v-else>
					Ładuję wiadomości...
				</div>
			</div>
		</div>
		<div class="wnl-chat-form">
			<wnl-message-form :socket="socket" :room="room" :inputId="inputId"></wnl-message-form>
		</div>
	</div>
</template>
<style lang="sass">
	@import '../../../sass/variables'

	.wnl-chat-messages
		height: 400px
		overflow-y: auto

	.wnl-chat-form
		border-top: $border-light-gray
		margin-top: 20px
		padding-top: 20px
</style>
<script>
	import Message from './Message.vue'
	import MessageForm from './MessageForm.vue'
	import * as socket from '../../socket'
	import { nextTick } from 'vue'

	export default {
		props: ['room'],
		data(){
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
			containerId() {
				return 'wnl-chat-room-' + this.room
			},
			container() {
				return document.getElementById(this.containerId)
			},
			contentId() {
				return 'wnl-chat-content-' + this.room
			},
			content() {
				return document.getElementById(this.contentId)
			},
			inputId() {
				return 'wnl-chat-form-' + this.room
			},
			input() {
				return document.getElementById(this.inputId)
			}
		},
		methods: {
			chatJoinRoom() {
				this.socket = socket.getSocket()
				this.socket.on('connected', (data) => {
					this.socket.emit('join-room', {
						room: this.room
					})
					this.socket.on('join-room-success', (data) => {
						if (!this.loaded) {
							this.setListeners()
							this.messages = data.messages
							this.loaded = true
							nextTick(() => {
								this.scrollToBottom()
							})
						}
					})
				})
			},
			setListeners() {
				this.socket.on('user-sent-message', (data) => {
					this.addMessage(data.message)
				})

				this.socket.on('message-processed', (data) => {
					if (data.sent) {
						this.addMessage(data.message)
					}
				})

				this.socket.on('error', (data) => {
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
		created () {
			this.chatJoinRoom()
		}
	}

</script>
