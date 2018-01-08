<template lang="html">
	<div class="conversation-list">
		<wnl-conversation-snippet
			v-for="(room, index) in rooms"
			:key="index"
			:room="room"
			:users="getRoomProfiles(room.profiles)"
			:currentRoom="currentRoom">

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
				profiles: [],
				messages: [],
				currentRoom: ''
			}
		},
		computed: {
			...mapGetters(['currentUserId']),
		},
		methods: {
			getPrivateRooms() {
				const path = 'chat_rooms/.getPrivateRooms?include=profiles'
				return axios.get(getApiUrl(path))
					.then(res => {
						this.profiles = res.data.included.profiles
						delete res.data.included
						this.rooms = Object.values(res.data)
					})
			},
			getMessages() {

			},
			getRoomName(interlocutors) {
				if (!Array.isArray(interlocutors)){
					interlocutors = interlocutors.split('-')
				}

				interlocutors = interlocutors.filter(id => {
					return parseInt(id) !== this.currentUserId
				})
				interlocutors.push(this.currentUserId)
				interlocutors = interlocutors.sort()
				interlocutors = interlocutors.join('-')

				return `private-${interlocutors}`
			},
			openRoom(interlocutors) {
				let newRoom

				if (interlocutors) {
					newRoom = this.getRoomName(interlocutors)
					let existingRoom = this.rooms.find(room => {
						return room.channel === newRoom
					})

					if (existingRoom) {
						this.switchToRoom(existingRoom)
					} else {
						this.startNewRoom(newRoom)
					}
				} else if (this.rooms.length > 0){
					this.switchToRoom(this.rooms[0])
				}
			},
			switchToRoom(room) {
				this.currentRoom = room
				this.emitRoomChange(room)
			},
			startNewRoom(roomName) {
				// create room & request profiles
//				this.rooms.unshift({channel: roomName})
//				this.$emit('roomSwitch', {channel: roomName})
			},
			emitRoomChange(room) {
				let users = this.getRoomProfiles(room.profiles)
				this.$emit('roomSwitch', {room, users})
			},
			getRoomProfiles(profileIds) {
				let profiles = [];

				profileIds.forEach((profileId) => {
					let profile = this.profiles[profileId]
					if (profile.user_id !== this.currentUserId) {
						profiles.push(profile)
					}
				})

				return profiles
			}
		},
		mounted(){
			this.getPrivateRooms()
				.then(() => this.openRoom(this.$route.params.interlocutors))
				.then(() => this.getMessages())
		},
		watch: {
			'$route' (to, from) {
				this.openRoom(to.params.interlocutors)
			}
		}
	}
</script>