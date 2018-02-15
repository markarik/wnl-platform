import {set} from 'vue'
import * as types from '../mutations-types'
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
		// i added this as a quick fix for checking a certain value in getRoomForPrivateChat,
		// if conditional couldn't process !profile.id
		if (Object.values(state.profiles).find(profile => profile.user_id === id)) {
			return Object.values(state.profiles).find(profile => profile.user_id === id)
		} else {
			return 0
		}
	},
	getRoomForPrivateChat: (state, getters, rootState, rootGetters) => userId => {
		const profile = getters.getProfileByUserId(userId)
		if (profile === 0) {
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
	[types.CHAT_ADD_ROOM] (state, payload) {
		state.sortedRooms.splice(0, 0, payload.data.id)
		set (state.rooms, payload.data.id, payload.data)
		set (state.profiles, payload.profile.id, payload.profile)
	},
	[types.CHAT_MESSAGES_SET_ROOM_MESSAGES] (state, {roomId, messages}) {
		set(state.rooms[roomId], 'messages', messages)
	},
	[types.CHAT_MESSAGES_READY] (state, isReady) {
		set(state, 'ready', isReady)
	},
	[types.CHAT_MESSAGES_ADD_MESSAGE] (state, {message, room}) {
		state.rooms[room].messages.splice(0, 0, message)
	}
}

//Actions
const actions = {
	async initChatMessages({commit, getters}) {
		const payload = await fetchUserRooms()
		commit(types.CHAT_MESSAGES_SET_ROOMS, payload)

		const roomsWithMessages = await fetchRoomsMessages(getters.sortedRooms)
		Object.keys(roomsWithMessages)
			.forEach(roomId => commit(types.CHAT_MESSAGES_SET_ROOM_MESSAGES, {roomId, messages: roomsWithMessages[roomId]}))

		commit(types.CHAT_MESSAGES_READY, true)
	},
	onNewMessage({commit}, {sent, ...payload}) {
		if (sent) {
			commit(types.CHAT_MESSAGES_ADD_MESSAGE, payload)
		}
	},
	async createNewRoom({commit, rootGetters}, {users}) {
		const {data: {included, ...data}} = await axios.post(getApiUrl('chat_rooms/.createPrivateRoom?include=profiles'), {
			name: `private-${users.join('-')}`,
			users
		})

		const profile = Object.values(included.profiles)
			.find(profile => profile.user_id !== rootGetters.currentUser.id)

		const payload = {
			data: {
				...data,
				messages: []
			},
			profile
		}

		commit(types.CHAT_ADD_ROOM, payload)

		return data
	}
}

const fetchUserRooms = async () => {
	const response = await axios.get(getApiUrl('chat_rooms/.getPrivateRooms?include=profiles'))
	if (response.data.length === 0) return {
		rooms: {},
		sortedRooms: [],
		profiles: {}
	}

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

	return payload
}

const fetchRoomsMessages = async (roomsIds) => {
	const {data} = await axios.post(getApiUrl('chat_messages/.getByRooms'), {
		rooms: roomsIds
	})
	const rooms = {}

	data.forEach(message => {
		if (!rooms[message.chat_room_id]) {
			rooms[message.chat_room_id] = []
		}

		rooms[message.chat_room_id].push(message)
	})

	return rooms
}

export default {
	namespaced,
	state,
	getters,
	mutations,
	actions
}
