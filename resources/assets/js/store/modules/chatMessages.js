import _ from 'lodash'
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
	connected: false,
}

//Getters
const getters = {
	getUnseenRooms: (state, getters) => {
		return Object.values(getters.rooms).reduce((sum, room) => {
			if (room.unread_count) return sum + 1
			return sum
		}, 0)
	},
	rooms: state => state.rooms,
	sortedRooms: state => state.sortedRooms,
	profiles: state => state.profiles,
	status: state => state.connected,
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
	getRoomMessagesPagination: (state, getters) => roomId => {
		const room = getters.getRoomById(roomId)
		return room.pagination || {}
	},
	getRoomBySlug: state => slug => Object.values(state.rooms).find(room => room.slug === slug),
	ready: state => state.ready
}

//mutations
const mutations = {
	[types.CHAT_MESSAGES_SET_STATUS] (state, payload) {
		set (state, 'connected', payload)
	},
	[types.CHAT_MESSAGES_SET_ROOMS] (state, data) {
		set (state, 'rooms', data.rooms)
		set (state, 'sortedRooms', data.sortedRooms)
		set (state, 'profiles', data.profiles)
	},
	[types.CHAT_MESSAGES_ADD_ROOM](state, payload) {
		set(state.rooms, payload.room.id, payload.room)
	},
	[types.CHAT_MESSAGES_ADD_PROFILES](state, profiles) {
		profiles.forEach(profile => {
			set(state.profiles, profile.id, profile)
		})
	},
	[types.CHAT_MESSAGES_SET_ROOM_MESSAGES](state, {roomId, messages, pagination}) {
		set(state.rooms[roomId], 'messages', messages)
		if (pagination) {
			set(state.rooms[roomId], 'pagination', pagination)
		}
	},
	[types.CHAT_MESSAGES_READY](state, isReady) {
		set(state, 'ready', isReady)
	},
	[types.CHAT_MESSAGES_ADD_MESSAGES](state, {messages, roomId, pagination}) {
		state.rooms[roomId].messages = messages.concat(state.rooms[roomId].messages)
		if (pagination) {
			set(state.rooms[roomId], 'pagination', pagination)
		}
	},
	[types.CHAT_MESSAGES_ADD_MESSAGE](state, {message, room}) {
		state.rooms[room].last_message_time = message.time
		state.rooms[room].messages.push(message)
	},
	[types.CHAT_MESSAGES_CHANGE_ROOM_SORTING](state, {room, newIndex}) {
		const currentIndex = state.sortedRooms.indexOf(room)
		if (currentIndex < 0) {
			state.sortedRooms.splice(0, 0, room)
		} else {
			state.sortedRooms.splice(currentIndex, 1)
			state.sortedRooms.splice(newIndex, 0, room)
		}
	},
	[types.CHAT_MESSAGES_ROOM_INCREMENT_UNREAD] (state, roomId) {
		state.rooms[roomId].unread_count++
	},
	[types.CHAT_MESSAGES_MARK_ROOM_AS_READ] (state, roomId) {
		state.rooms[roomId].unread_count = 0
	}
}

//Actions
const actions = {
	async initChatMessages({commit, getters}) {
		const payload = await fetchUserRooms()
		commit(types.CHAT_MESSAGES_SET_ROOMS, payload)

		if (payload.sortedRooms.length === 0) return commit(types.CHAT_MESSAGES_READY, true)

		const roomsWithMessages = await fetchRoomsMessages(getters.sortedRooms)
		Object.keys(roomsWithMessages)
			.forEach(roomId => commit(types.CHAT_MESSAGES_SET_ROOM_MESSAGES, {
				roomId,
				messages: roomsWithMessages[roomId]
			}))

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
		if (message.user_id !== rootGetters.currentUserId) {
			commit(types.CHAT_MESSAGES_ROOM_INCREMENT_UNREAD, room)
		}
	},
	setConnectionStatus({commit}, payload) {
		commit(types.CHAT_MESSAGES_SET_STATUS, payload)
	},
	async createPrivateRoom({commit, rootGetters, state}, {users}) {
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
		commit(types.CHAT_MESSAGES_CHANGE_ROOM_SORTING, {
			room: room.id,
			newIndex: 0
		})
		commit(types.CHAT_MESSAGES_ADD_PROFILES, Object.values(included.profiles))

		return room
	},
	async createPublicRoom({commit, getters}, {slug}) {
		const existingRoom = getters.getRoomBySlug(slug)

		if (existingRoom) {
			return existingRoom
		}

		const url = getApiUrl('chat_rooms/.createPublicRoom')
		const response = await axios.post(url, {slug})
		const room = response.data
		const payload = {
			room: {
				...room,
				messages: []
			}
		}
		commit(types.CHAT_MESSAGES_ADD_ROOM, payload)

		return room
	},
	async fetchRoomMessages({commit}, {room, currentCursor, limit, context}) {
		let response = {}
		if (context.messageTime && context.roomId) {
			response = await fetchRoomMessagesWithContext(context)
		} else {
			response = await fetchPaginatedRoomMessages(room.id, currentCursor, limit)
		}
		const {messages, profiles, cursor} = response

		if (!cursor) {
			commit(types.CHAT_MESSAGES_SET_ROOM_MESSAGES, {
				roomId: room.id,
				messages,
				pagination: cursor
			})
		} else {
			commit(types.CHAT_MESSAGES_ADD_MESSAGES, {
				roomId: room.id,
				messages,
				pagination: cursor
			})
		}

		commit(types.CHAT_MESSAGES_ADD_PROFILES, profiles)

		return messages
	},
	markRoomAsRead({commit}, roomId) {
		commit(types.CHAT_MESSAGES_MARK_ROOM_AS_READ, roomId)
	}
}

const fetchUserRooms = async () => {
	const response = await axios.get(getApiUrl('chat_rooms/.getPrivateRooms'), {
		params: {
			include: 'profiles'
		}
	})
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

const fetchPaginatedRoomMessages = async (roomId, currentCursor, limit = 10) =>  {
	const {data} = await axios.post(getApiUrl('chat_messages/.getByRooms'), {
		rooms: [roomId],
		include: 'profiles',
		limit,
		currentCursor
	})

	return serializeResponse(data)
}

const fetchRoomsMessages = async (roomsIds, limit = 50) => {
	const {data: {data, cursor}} = await axios.post(getApiUrl('chat_messages/.getByRooms'), {
		rooms: roomsIds,
		limit
	})
	const rooms  = {}

	data.reverse().forEach(message => {
		if (!rooms[message.chat_room_id]) {
			rooms[message.chat_room_id] = []
		}

		rooms[message.chat_room_id].push(message)
	})

	return rooms
}

const fetchRoomMessagesWithContext = async ({messageTime, roomId}) => {
	const {data} = await axios.post(getApiUrl('chat_messages/.getWithContext'), {
		include: 'profiles',
		messageTime,
		roomId
	})

	return serializeResponse(data)
}

const serializeResponse = (data) => {
	const {cursor, data: response} = data
	const {included = {}, ...messages} = response

	return {
		profiles: Object.values(included.profiles || {}),
		messages: Object.values(messages).reverse(),
		cursor
	}
}

export default {
	namespaced,
	state,
	getters,
	mutations,
	actions
}
