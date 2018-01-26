<template lang="html">
	<div class="conversation-list" v-if="rooms.length">
		<wnl-conversation-snippet
			v-for="(room, index) in rooms"
			:key="index"
			:room="room"
			:users="getRoomProfiles(room.profiles)"
			:messages="getRoomMessages(room)"
			:currentRoom="currentRoom">
		</wnl-conversation-snippet>
	</div>
	<span v-else>Nie masz żadnych rozmów</span>
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
	import {isEmpty} from 'lodash'

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
					.then(({data}) => {
						if (isEmpty(data)) return

						this.profiles = data.included.profiles
						delete data.included
						this.rooms = Object.values(data)
					})
			},
			getMessages() {
				const path = 'chat_messages/.getByRooms'
				const data = {
					rooms: this.rooms.map(val => val.id)
				}
				return axios.post(getApiUrl(path), data)
					.then(res => {
						this.messages = res.data
						return res
					})

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
				return new Promise((resolve, reject) => {
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
					} else if (this.rooms.length > 0) {
						this.switchToRoom(this.rooms[0])
					}

					resolve()
				})
			},
			switchToRoom(room) {
				this.currentRoom = room
				this.emitRoomChange(room)
			},
			startNewRoom(roomName) {
				const path = 'chat_rooms/.createPrivateRoom?include=profiles'
				const data = {
					name: roomName
				}
				axios.post(getApiUrl(path), data)
					.then(res => {
						let profiles = res.data.included.profiles
						this.profiles = {...this.profiles, ...profiles}
						delete res.data.included
						let room = res.data
						this.rooms.unshift(room)
						this.switchToRoom(room)
					})
			},
			emitRoomChange(room) {
				let users = this.getRoomProfiles(room.profiles)
				let messages = this.getRoomMessages(room)
				this.$emit('roomSwitch', {room, users, messages})
			},
			getRoomMessages(room) {
					return this.messages.filter(m => m.chat_room_id === room.id)
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
				.then(() => this.rooms.length && this.getMessages(this.rooms))
				.then(() => this.openRoom(this.$route.params.interlocutors))
		},
		watch: {
			'$route' (to, from) {
				this.openRoom(to.params.interlocutors)
			}
		}
	}
</script>
