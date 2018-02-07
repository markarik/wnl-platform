<template lang="html">
	<div class="conversation-list" v-if="sortedRooms.length">
		<wnl-conversation-snippet
			v-for="roomId in sortedRooms"
			:key="roomId"
			:room="getRoomById(roomId)"
			:users="getRoomProfiles(roomId)"
			:messages="getRoomMessages(roomId)"
		/>
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
				currentRoom: ''
			}
		},
		computed: {
			...mapGetters(['currentUserId']),
			...mapGetters('chatMessages', ['getRoomById', 'sortedRooms', 'getRoomProfiles', 'getRoomMessages']),
		},
		methods: {
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
			}
		},
		watch: {
			'$route' (to, from) {
				this.openRoom(to.params.interlocutors)
			}
		}
	}
</script>
