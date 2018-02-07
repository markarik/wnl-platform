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
	[types.CHAT_MESSAGES_SET_ROOMS_WITH_MESSAGES] (state, data) {
		set(state, 'rooms', data)
	}
}

//Actions
const actions = {
	fetchInitialState({commit, getters}) {
		axios.get(getApiUrl('chat_rooms/.getPrivateRooms?include=profiles'))
			.then((response) => {
				const {included, ...rooms} = response.data
				const payload = {
					rooms: {},
					sortedRooms: [],
					profiles: included.profiles
				}

				Object.values(rooms).forEach((room) => {
					payload.rooms[room.id] = room
					payload.sortedRooms.push(room.id)
				})

				commit(types.CHAT_MESSAGES_SET_ROOMS, payload)
			}).then(() => {
				axios.post(getApiUrl('chat_messages/.getByRooms'), {
					rooms: getters.sortedRooms
				}).then(({data}) => {
					const rooms = getters.rooms

					data.forEach(message => {
						if (!rooms[message.chat_room_id]) {
							rooms[message.chat_room_id] = {}
						}

						if (!rooms[message.chat_room_id].messages) {
							rooms[message.chat_room_id].messages = []
						}

						rooms[message.chat_room_id].messages.push(message)
					})

					commit(types.CHAT_MESSAGES_SET_ROOMS_WITH_MESSAGES, rooms)
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
