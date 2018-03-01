import {set} from 'vue'
import {uniq} from 'lodash'

import * as types from '../mutations-types'
import {getApiUrl} from 'js/utils/env'

const namespaced = true

//Initial state
const state = {
	rooms: {},
	sortedRooms: [],
	profiles: {},
	ready: false,
	hasMoreRooms: false,
}

//Getters
const getters = {
	rooms: state => state.rooms,
	sortedRooms: state => state.sortedRooms,
	profiles: state => state.profiles,
	hasMoreRooms: state => state.hasMoreRooms,
	getRoomById: state => id => state.rooms[id] || {},
	getProfileById: state => id => state.profiles[id] || {},
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
		return Object.values(state.profiles).find(profile => profile.user_id === id) || {}
	},
	getInterlocutor: (state, getters, rootState, rootGetters) => profiles => {
		const profileId = profiles.find(profileId => profileId !== rootGetters.currentUser.id)
		if (profileId) return getters.getProfileById(profileId)
		return {}
	},
	getRoomForPrivateChat: (state, getters, rootState, rootGetters) => userId => {
		const profile = getters.getProfileByUserId(userId)
		if (!profile.id) {
			return {}
		}

		if (profile.id === rootGetters.currentUser.id) {
			return Object.values(state.rooms).find(room => {
				const roomProfiles = room.profiles || []

				return roomProfiles.length === 1 && roomProfiles.includes(profile.id)
			}) || {}
		}

		return Object.values(state.rooms).find(room => {
			const roomProfiles = room.profiles || []

			return roomProfiles.length === 2 &&
				roomProfiles.includes(rootGetters.currentUser.id) &&
				roomProfiles.includes(profile.id)
		}) || {}
	},
	ready: state => state.ready
}

//mutations
const mutations = {
	[types.CHAT_MESSAGES_HAS_MORE_ROOMS] (state, data) {
		set (state, 'hasMoreRooms', data)
	},
	[types.CHAT_MESSAGES_SET_ROOMS] (state, data) {
		console.log(data, '...chat messages set rooms');
		set (state, 'rooms', data.rooms)
		set (state, 'sortedRooms', data.sortedRooms)
		set (state, 'profiles', data.profiles)
	},
	[types.CHAT_MESSAGES_ADD_ROOM] (state, payload) {
		set (state.rooms, payload.room.id, payload.room)
	},
	[types.CHAT_MESSAGES_ADD_PROFILES] (state, profiles) {
		profiles.forEach(profile => {
			set(state.profiles, profile.id, profile)
		})
	},
	[types.CHAT_MESSAGES_SET_ROOM_MESSAGES] (state, {roomId, messages}) {
		set(state.rooms[roomId], 'messages', messages)
	},
	[types.CHAT_MESSAGES_READY] (state, isReady) {
		set(state, 'ready', isReady)
	},
	[types.CHAT_MESSAGES_ADD_MESSAGE] (state, {message, room}) {
		state.rooms[room].last_message_time = message.time
		state.rooms[room].messages.push(message)
	},
	[types.CHAT_MESSAGES_CHANGE_ROOM_SORTING] (state, {room, newIndex}) {
		const currentIndex = state.sortedRooms.indexOf(room)
		if (currentIndex < 0) {
			state.sortedRooms.splice(0,0, room)
		} else {
			state.sortedRooms.splice(currentIndex, 1)
			state.sortedRooms.splice(newIndex, 0, room)
		}
	},
	[types.CHAT_MESSAGES_ADD_PULLED_ROOM] (state, payload) {
		console.log(payload.id, '...payload mutacji');
		console.log(state.rooms, '...state.rooms');
		set (state.rooms, payload.id, payload)
	},
	[types.CHAT_MESSAGES_ADD_PULLED_ROOM_TO_SORTED] (state, payload) {
		console.log(payload, '... sorted rooms');
		state.sortedRooms.push(payload[0])
	},
	[types.CHAT_MESSAGES_ADD_PULLED_PROFILE] (state, payload) {
		console.log(payload, '... profiles');
		Object.keys(payload).forEach((key) => {
			console.log(payload[key].user_id);
			set (state.profiles, payload[key].user_id, payload[key])
		})
	}
}

//Actions
const actions = {
	async pullUserRooms({commit}, data) {
		console.log(data);
		const payload = await fetchUserRooms(data)
		console.log(payload, '...payload pull user rooms');
		commit(types.CHAT_MESSAGES_ADD_PULLED_ROOM, Object.values(payload.payload.rooms)[0])
		commit(types.CHAT_MESSAGES_HAS_MORE_ROOMS, payload.hasMoreRooms)
		commit(types.CHAT_MESSAGES_ADD_PULLED_ROOM_TO_SORTED, payload.payload.sortedRooms)
		commit(types.CHAT_MESSAGES_ADD_PULLED_PROFILE, payload.payload.profiles)
	},
	async initChatMessages({commit, getters}) {
		const payload = await fetchUserRooms({limit: 3, page: 0})
		console.log(payload, '.... init rooms');
		commit(types.CHAT_MESSAGES_SET_ROOMS, payload.payload)
		commit(types.CHAT_MESSAGES_HAS_MORE_ROOMS, payload.hasMoreRooms)

		if (payload.sortedRooms.length === 0) return commit(types.CHAT_MESSAGES_READY, true)

		const roomsWithMessages = await fetchRoomsMessages(getters.sortedRooms)
		Object.keys(roomsWithMessages)
			.forEach(roomId => commit(types.CHAT_MESSAGES_SET_ROOM_MESSAGES, {roomId, messages: roomsWithMessages[roomId]}))

		commit(types.CHAT_MESSAGES_READY, true)
	},
	onNewMessage({commit, getters, rootGetters}, {room, users: profiles, message}) {

		if (!getters.getRoomById(room).id) {
			commit(types.CHAT_MESSAGES_ADD_ROOM, {
				room: {
					messages: [],
					profiles: profiles.map(profile => profile.id),
					id: room
				}
			})
			commit(types.CHAT_MESSAGES_ADD_PROFILES, profiles)
		}

		commit(types.CHAT_MESSAGES_ADD_MESSAGE, {room, message})
		commit(types.CHAT_MESSAGES_CHANGE_ROOM_SORTING, {room, newIndex: 0})
	},
	async createNewRoom({commit, rootGetters, state}, {users}) {
		const uniqUsers = uniq(users)
		const response = await axios.post(getApiUrl('chat_rooms/.createPrivateRoom'), {
			name: `private-${uniqUsers.join('-')}`,
			include: 'profiles',
			users: uniqUsers,
		})
		const {included, ...room} = response.data

		const payload = {
			room: {
				...room,
				messages: []
			}
		}

		commit(types.CHAT_MESSAGES_ADD_ROOM, payload)
		commit(types.CHAT_MESSAGES_CHANGE_ROOM_SORTING, {room: room.id, newIndex: 0})
		commit(types.CHAT_MESSAGES_ADD_PROFILES, Object.values(included.profiles))

		return room
	}
}

const fetchUserRooms = async (data) => {
	console.log(data);
	const response = await axios.get(getApiUrl('chat_rooms/.getPrivateRooms'), {
		params: {
			include: 'profiles',
			limit: data.limit,
			page: data.page
		}
	})
	if (response.data.data.length === 0) return {
		rooms: {},
		sortedRooms: [],
		profiles: {}
	}

	const {included, ...rooms} = response.data.data
	const payload = {
		rooms: {},
		sortedRooms: [],
		profiles: included.profiles
	}
	const hasMoreRooms = response.data.has_more

	Object.values(rooms).forEach((room) => {
		payload.rooms[room.id] = {
			...room, messages: []
		}
		payload.sortedRooms.push(room.id)
	})
	return {payload, hasMoreRooms}
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
