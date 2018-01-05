<template lang="html">
	<div class="conversation-list">
		<wnl-conversation-snippet
			v-for="(room, index) in rooms"
			:key="index"
			:room="room">

		</wnl-conversation-snippet>
	</div>
</template>

<style lang="sass">
	.conversation-list
		display: flex
		flex-direction: column
</style>

<script>
	import axios from 'axios'
	import ConversationSnippet from 'js/components/messages/ConversationSnippet'
	import {getApiUrl} from 'js/utils/env'
	import {mapGetters} from 'vuex'

	export default {
		name: 'ConversationsList',
		components: {
			'wnl-conversation-snippet': ConversationSnippet
		},
		data() {
			return  {
				rooms: [],
				currentRoom: ''
			}
		},
		computed: {
			...mapGetters(['currentUserId'])
		},
		methods: {
			getPrivateRooms() {
				return axios.get(getApiUrl('chat_rooms/.getPrivateRooms'))
					.then(response => {
						this.rooms = response.data
						return response
					})
			},
			getMessages() {

			},
			getRoomName(interlocutors) {
				if (!Array.isArray(interlocutors)){
					interlocutors = interlocutors.split('-')
				}
				interlocutors.push(this.currentUserId)
				interlocutors = interlocutors.sort()
				interlocutors = interlocutors.join('-')

				return `private-${interlocutors}`
			},
			openRoom() {
				let newRoom,
					interlocutors = this.$route.params.interlocutors

				if (interlocutors) {
					newRoom = this.getRoomName(interlocutors)
					if (this.rooms.find(room => room.channel === newRoom)) {
						this.switchToRoom(newRoom)
					} else {
						this.startNewRoom(newRoom)
					}
				} else {
					// open last conversation
				}
			},
			switchToRoom(roomName) {
				this.currentRoom = {channel: roomName}
				this.$emit('roomSwitch', {channel: roomName})
			},
			startNewRoom(roomName) {
				this.rooms.unshift({channel: roomName})
				this.$emit('roomSwitch', {channel: roomName})
			}
		},
		mounted(){
			this.getPrivateRooms()
				.then(() => this.openRoom())
//			Promise.all([
//				this.getPrivateRooms(),
//				this.getMessages(),
//				this.openRoom()
//			])
		},
		watch: {
			'$route' (to, from) {

			}
		}
	}
</script>