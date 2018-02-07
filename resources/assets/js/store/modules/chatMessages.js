import {set} from 'vue'
import * as types from '../mutations-types'
import * as socket from '../../socket'
import {getApiUrl} from 'js/utils/env'

const namespaced = true

//Initial state
const state = {
	rooms: {},
	sortedRooms: [],
	profiles: {}
}

//Getters
const getters = {
	rooms: state => state.rooms,
	sortedRooms: state => state.sortedRooms,
	profiles: state => state.profiles,
	getRoomById: state => id => state.rooms[id] || {},
	getRoomProfiles: (state, getters) => id => {
		const profileIds = getters.getRoomById(id).profiles || []

		return profileIds.map(profileId => {
			return state.profiles[profileId]
		})
	},
	getRoomMessages: (state, getters) => id => {
		return getters.getRoomById(id).messages || []
	}
}

//mutations
const mutations = {
	[types.CHAT_MESSAGES_SET_ROOMS] (state, data) {
		set (state, 'rooms', data.rooms)
		set (state, 'sortedRooms', data.sortedRooms)
		set (state, 'profiles', data.profiles)
	},
	[types.CHAT_MESSAGES_SET_ROOM_MESSAGES] (state, {roomId, messages}) {
		set(state.rooms[roomId], 'messages', messages)
	},
}

//Actions
const actions = {
	fetchInitialState({commit, getters, state}) {
		axios.get(getApiUrl('chat_rooms/.getPrivateRooms?include=profiles'))
			.then((response) => {
				const {included, ...rooms} = response.data
				const payload = {
					rooms: {},
					sortedRooms: [],
					profiles: included.profiles
				}

				Object.values(rooms).forEach((room) => {
					payload.rooms[room.id] = {
						...room, messages: []
					}
					payload.sortedRooms.push(room.id)
				})

				commit(types.CHAT_MESSAGES_SET_ROOMS, payload)
			}).then(() => {
				axios.post(getApiUrl('chat_messages/.getByRooms'), {
					rooms: getters.sortedRooms
				}).then(({data}) => {
					const rooms = {}

					data.forEach(message => {
						if (!rooms[message.chat_room_id]) {
							rooms[message.chat_room_id] = []
						}

						rooms[message.chat_room_id].push(message)
					})

					Object.keys(rooms).forEach(roomId => commit(types.CHAT_MESSAGES_SET_ROOM_MESSAGES, {roomId, messages: rooms[roomId]}))
				})
			})
	},
}
export default {
	namespaced,
	state,
	getters,
	mutations,
	actions
}
