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
	currentPage: 1,
}

//Getters
const getters = {
	rooms: state => state.rooms,
	sortedRooms: state => state.sortedRooms,
	profiles: state => state.profiles,
	hasMoreRooms: state => state.hasMoreRooms,
	currentPage: state => state.currentPage,
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
		Object.keys(payload).forEach((key) => {
			console.log(payload[key], 'roomy w mutacji');
			set (state.rooms, payload[key].id, payload[key])
		})
	},
	[types.CHAT_MESSAGES_ADD_PULLED_ROOM_TO_SORTED] (state, payload) {
		console.log(payload, 'sorted rooms w mutacji');
		state.sortedRooms = state.sortedRooms.concat(payload)
	},
	[types.CHAT_MESSAGES_ADD_PULLED_PROFILE] (state, payload) {
		Object.keys(payload).forEach((key) => {
			console.log(payload[key], 'profile w mutacji');
			set (state.profiles, payload[key].user_id, payload[key])
		})
	},
	[types.CHAT_MESSAGES_INCREMENT_PAGE] (state, payload) {
		state.currentPage += payload
	}
}

//Actions
const actions = {
	async pullUserRooms({commit, getters}, data) {
		const response = await fetchUserRooms(data)
		commit(types.CHAT_MESSAGES_ADD_PULLED_ROOM, response.payload.rooms)
		commit(types.CHAT_MESSAGES_HAS_MORE_ROOMS, response.hasMoreRooms)

		// if (_.includes(getters.sortedRooms, response.payload.sortedRooms)) {
		// 	return []
		// } else {
		commit(types.CHAT_MESSAGES_ADD_PULLED_ROOM_TO_SORTED, response.payload.sortedRooms)
		// }

		commit(types.CHAT_MESSAGES_ADD_PULLED_PROFILE, response.payload.profiles)

		const roomsWithMessages = await fetchRoomsMessages(getters.sortedRooms)
		Object.keys(roomsWithMessages)
			.forEach(roomId => commit(types.CHAT_MESSAGES_SET_ROOM_MESSAGES, {roomId, messages: roomsWithMessages[roomId]}))

		commit(types.CHAT_MESSAGES_READY, true)
	},
	async initChatMessages({commit, getters}) {
		const response = await fetchUserRooms({limit: 10, page: getters.currentPage})
		commit(types.CHAT_MESSAGES_SET_ROOMS, response.payload)
		commit(types.CHAT_MESSAGES_HAS_MORE_ROOMS, response.hasMoreRooms)

		if (response.payload.sortedRooms.length === 0) return commit(types.CHAT_MESSAGES_READY, true)

		const roomsWithMessages = await fetchRoomsMessages(getters.sortedRooms)
		Object.keys(roomsWithMessages)
			.forEach(roomId => commit(types.CHAT_MESSAGES_SET_ROOM_MESSAGES, {roomId, messages: roomsWithMessages[roomId]}))

		commit(types.CHAT_MESSAGES_READY, true)
	},
	incrementPage({commit}) {
		commit(types.CHAT_MESSAGES_INCREMENT_PAGE, 1)
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
