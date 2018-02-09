import {set} from 'vue'
import * as types from '../mutations-types'
import * as socket from '../../socket'
import {getApiUrl} from 'js/utils/env'

const namespaced = true

//Initial state
const state = {
	rooms: {},
	sortedRooms: [],
	profiles: {},
	ready: false
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
	},
	getProfileByUserId: state => id => {
		return Object.values(state.profiles).find(profile => profile.user_id === id)
	},
	getRoomForPrivateChat: (state, getters, rootState, rootGetters) => userId => {
		const profile = getters.getProfileByUserId(userId)
		if (!profile.id) {
			return {}
		}

		return Object.values(state.rooms).find(room => {
			const roomProfiles = room.profiles || []

			roomProfiles.length === 2 && _.difference(roomProfiles, [rootGetters.currentUser.id, profile.id]).length === 0
		}) || {}
	},
	userPrivateChannel: (state, getters, rootState, rootGetters) => `private-channel-${rootGetters.currentUserId}`,
	ready: state => state.ready
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
	[types.CHAT_MESSAGES_READY] (state, isReady) {
		set(state, 'ready', isReady)
	}
}

//Actions
const actions = {
	initChatMessages({commit, getters, state}) {
		return axios.get(getApiUrl('chat_rooms/.getPrivateRooms?include=profiles'))
			.then((response) => {
				if (response.data.length === 0) return

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
			}).then(() => axios.post(getApiUrl('chat_messages/.getByRooms'), {
					rooms: getters.sortedRooms
				})
			).then(({data}) => {
				const rooms = {}

				data.forEach(message => {
					if (!rooms[message.chat_room_id]) {
						rooms[message.chat_room_id] = []
					}

					rooms[message.chat_room_id].push(message)
				})

				Object.keys(rooms).forEach(roomId => commit(types.CHAT_MESSAGES_SET_ROOM_MESSAGES, {roomId, messages: rooms[roomId]}))
				commit(types.CHAT_MESSAGES_READY, true)
			})
	},
	onNewMessage({commit}, payload) {
		console.log(payload)
	}
}
export default {
	namespaced,
	state,
	getters,
	mutations,
	actions
}
