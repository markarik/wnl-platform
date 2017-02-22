<template>
	<div>
		<div v-if="loaded">
			<wnl-message v-for="message in messages" :username="message.username" :timeago="message.timeago">
				{{ message.content }}
			</wnl-message>
		</div>
		<div v-else>
			Ładuję te mesedże...
		</div>
		<div>
			<wnl-message-form :socket="socket"></wnl-message-form>
		</div>
	</div>
</template>
<style>


</style>
<script>
	import Message from './Message.vue'
	import MessageForm from './MessageForm.vue'
	import * as socket from '../../socket'

	export default {
		props: ['roomId'],
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
		methods: {
			chatJoinRoom() {
				this.socket = socket.getSocket()
				this.socket.on('connected', (data) => {
					this.socket.emit('join-room', {
						roomId: this.roomId
					});
					this.socket.on('join-room-success', (data) => {
						this.messages = data.messages
						this.loaded = true
					});
				})
			}
		},
		created () {
			this.chatJoinRoom()
		}
	}

</script>
