import _ from 'lodash';
import {set} from 'vue';
import {uniq} from 'lodash';

import * as types from 'js/store/mutations-types';
import {getApiUrl} from 'js/utils/env';
import {
	SOCKET_EVENT_SEND_MESSAGE,
	SOCKET_EVENT_MARK_ROOM_AS_READ,
	SOCKET_EVENT_USER_SENT_MESSAGE
} from 'js/plugins/chat-connection';

const namespaced = true;

//Initial state
const state = {
	rooms: {},
	sortedRooms: [],
	profiles: {},
	ready: false,
	pagination: {
		hasMoreRooms: false,
		currentPage: 1,
	},
	connected: true,
};

//Getters
const getters = {
	unreadConversations: (state, getters) => {
		return Object.values(getters.rooms).reduce((sum, room) => {
			if (room.unread_count) return sum + 1;
			return sum;
		}, 0);
	},
	rooms: state => state.rooms,
	sortedRooms: state => state.sortedRooms,
	profiles: state => state.profiles,
	hasMoreRooms: state => state.pagination.hasMoreRooms,
	currentPage: state => state.pagination.currentPage,
	status: state => state.connected,
	getRoomById: state => id => state.rooms[id] || {},
	getProfileById: state => id => state.profiles[id] || {},
	getRoomProfiles: (state, getters) => id => {
		const profileIds = getters.getRoomById(id).profiles || [];

		return profileIds.map(profileId => {
			return state.profiles[profileId];
		});
	},
	getRoomMessages: (state, getters) => id => {
		return getters.getRoomById(id).messages || [];
	},
	getProfileByUserId: state => id => {
		return Object.values(state.profiles).find(profile => profile.user_id === id) || {};
	},
	getInterlocutor: (state, getters, rootState, rootGetters) => profiles => {
		const profileId = profiles.find(profileId => profileId !== rootGetters.currentUserProfileId);
		if (profileId) return getters.getProfileById(profileId);
		return {};
	},
	getRoomForPrivateChat: (state, getters, rootState, rootGetters) => userId => {
		const profile = getters.getProfileByUserId(userId);
		if (!profile.id) {
			return {};
		}

		if (profile.id === rootGetters.currentUserProfileId) {
			return Object.values(state.rooms).find(room => {
				const roomProfiles = room.profiles || [];

				return roomProfiles.length === 1 && roomProfiles.includes(profile.id);
			}) || {};
		}

		return Object.values(state.rooms).find(room => {
			const roomProfiles = room.profiles || [];

			return roomProfiles.length === 2 &&
				roomProfiles.includes(rootGetters.currentUserProfileId) &&
				roomProfiles.includes(profile.id);
		}) || {};
	},
	getRoomBySlug: state => slug => Object.values(state.rooms).find(room => room.slug === slug),
	ready: state => state.ready
};

//mutations
const mutations = {
	[types.CHAT_MESSAGES_HAS_MORE_ROOMS] (state, data) {
		set (state.pagination, 'hasMoreRooms', data);
	},
	[types.CHAT_MESSAGES_SET_STATUS] (state, payload) {
		set (state, 'connected', payload);
	},
	[types.CHAT_MESSAGES_SET_ROOMS] (state, data) {
		set (state, 'rooms', data.rooms);
		set (state, 'sortedRooms', data.sortedRooms);
		set (state, 'profiles', data.profiles);
	},
	[types.CHAT_MESSAGES_ADD_ROOM](state, payload) {
		set(state.rooms, payload.room.id, payload.room);
	},
	[types.CHAT_MESSAGES_ADD_PROFILES](state, profiles) {
		profiles.forEach(profile => {
			set(state.profiles, profile.id, profile);
		});
	},
	[types.CHAT_MESSAGES_SET_ROOM_MESSAGES](state, {roomId, messages, pagination}) {
		set(state.rooms[roomId], 'messages', messages);
		if (pagination) {
			set(state.rooms[roomId], 'pagination', pagination);
		}
	},
	[types.CHAT_MESSAGES_READY](state, isReady) {
		set(state, 'ready', isReady);
	},
	[types.CHAT_MESSAGES_ADD_MESSAGES](state, {messages, roomId, pagination}) {
		state.rooms[roomId].messages = messages.concat(state.rooms[roomId].messages);
		if (pagination) {
			set(state.rooms[roomId], 'pagination', pagination);
		}
	},
	[types.CHAT_MESSAGES_ADD_MESSAGE](state, {message, roomId}) {
		state.rooms[roomId].last_message_time = message.time;
		state.rooms[roomId].messages.push(message);
	},
	[types.CHAT_MESSAGES_CHANGE_ROOM_SORTING](state, {roomId, newIndex}) {
		const currentIndex = state.sortedRooms.indexOf(roomId);
		if (currentIndex < 0) {
			state.sortedRooms.splice(0, 0, roomId);
		} else {
			state.sortedRooms.splice(currentIndex, 1);
			state.sortedRooms.splice(newIndex, 0, roomId);
		}
	},
	[types.CHAT_MESSAGES_ADD_ROOMS] (state, rooms) {
		rooms.forEach((room) => {
			set (state.rooms, room.id, room);
		});
	},
	[types.CHAT_MESSAGES_ADD_ROOMS_TO_SORTED] (state, payload) {
		state.sortedRooms = state.sortedRooms.concat(payload);
	},
	[types.CHAT_MESSAGES_SET_CURRENT_PAGE] (state, payload) {
		set (state.pagination, 'currentPage', payload);
	},
	[types.CHAT_MESSAGES_ROOM_INCREMENT_UNREAD] (state, roomId) {
		state.rooms[roomId].unread_count = (state.rooms[roomId].unread_count || 0) + 1;
	},
	[types.CHAT_MESSAGES_MARK_ROOM_AS_READ] (state, roomId) {
		if(state.rooms[roomId]) state.rooms[roomId].unread_count = 0;
	}
};

//Actions
const actions = {
	async fetchUserRoomsWithMessages({commit, getters}, {limit=20, page=1}) {
		const {payload, pagination, log_pointer} = await fetchUserRooms({limit, page});

		commit(types.CHAT_MESSAGES_ADD_PROFILES, Object.values(payload.profiles));
		commit(types.CHAT_MESSAGES_ADD_ROOMS, Object.values(payload.rooms));
		commit(types.CHAT_MESSAGES_ADD_ROOMS_TO_SORTED, payload.sortedRooms);

		commit(types.CHAT_MESSAGES_HAS_MORE_ROOMS, pagination.hasMoreRooms);
		commit(types.CHAT_MESSAGES_SET_CURRENT_PAGE, pagination.currentPage);

		if (payload.sortedRooms.length === 0) return commit(types.CHAT_MESSAGES_READY, true);

		const roomsWithMessages = await fetchRoomsMessages(payload.sortedRooms);

		Object.keys(roomsWithMessages).forEach(roomId => {
			commit(types.CHAT_MESSAGES_SET_ROOM_MESSAGES, {roomId, ...roomsWithMessages[roomId]});
		});

		commit(types.CHAT_MESSAGES_READY, true);

		return log_pointer;
	},
	onNewMessage({commit, getters, rootGetters}, {room, users: profiles = [], message}) {
		const isPrivateRoom = room.type === 'private';
		const roomId = room.id;

		if (!getters.getRoomById(roomId).id) {
			commit(types.CHAT_MESSAGES_ADD_ROOM, {
				room: {
					...room,
					messages: [],
					profiles: profiles.map(profile => profile.id),
					unread_count: 0
				}
			});
		}

		commit(types.CHAT_MESSAGES_ADD_PROFILES, profiles);
		commit(types.CHAT_MESSAGES_ADD_MESSAGE, {roomId, message});

		if (isPrivateRoom) commit(types.CHAT_MESSAGES_CHANGE_ROOM_SORTING, {roomId, newIndex: 0});

		if (isPrivateRoom && message.user_id !== rootGetters.currentUserId) {
			commit(types.CHAT_MESSAGES_ROOM_INCREMENT_UNREAD, roomId);
		}
	},
	setConnectionStatus({commit}, payload) {
		commit(types.CHAT_MESSAGES_SET_STATUS, payload);
	},
	async createPrivateRoom({commit, rootGetters, state}, {users}) {
		const uniqUsers = uniq(users);
		const response = await axios.post(getApiUrl('chat_rooms/.createPrivateRoom'), {
			name: `private-${uniqUsers.join('-')}`,
			include: 'profiles',
			users: uniqUsers,
		});
		const {included, ...room} = response.data;

		const payload = {
			room: {
				...room,
				messages: []
			}
		};

		commit(types.CHAT_MESSAGES_ADD_ROOM, payload);
		commit(types.CHAT_MESSAGES_CHANGE_ROOM_SORTING, {
			roomId: room.id,
			newIndex: 0
		});
		commit(types.CHAT_MESSAGES_ADD_PROFILES, Object.values(included.profiles));

		return room;
	},
	async createPublicRoom({commit, getters}, {slug}) {
		const existingRoom = getters.getRoomBySlug(slug);

		if (existingRoom) {
			return existingRoom;
		}

		const url = getApiUrl('chat_rooms/.createPublicRoom');
		const response = await axios.post(url, {slug});
		const room = response.data;
		const payload = {
			room: {
				...room,
				messages: []
			}
		};
		commit(types.CHAT_MESSAGES_ADD_ROOM, payload);
		return room;
	},
	async fetchRoomMessages({commit}, {room, currentCursor, limit, context = {}, append = false}) {
		let response = {};
		if (context.messageTime && context.roomId) {
			response = await fetchRoomMessagesWithContext(context);
		} else {
			response = await fetchPaginatedRoomMessages(room.id, currentCursor, limit);
		}
		const {messages, profiles, cursor} = response;

		if (!append) {
			commit(types.CHAT_MESSAGES_SET_ROOM_MESSAGES, {
				roomId: room.id,
				messages,
				pagination: cursor
			});
		} else {
			commit(types.CHAT_MESSAGES_ADD_MESSAGES, {
				roomId: room.id,
				messages,
				pagination: cursor
			});
		}

		commit(types.CHAT_MESSAGES_ADD_PROFILES, profiles);

		return {messages, pagination: cursor};
	},
	markRoomAsRead({commit}, roomId) {
		commit(types.CHAT_MESSAGES_MARK_ROOM_AS_READ, roomId);
	},

	updateFromEventLog({commit, dispatch}, events) {
		events.forEach(event => {
			switch (event.name) {
			case SOCKET_EVENT_SEND_MESSAGE:
				dispatch('onNewMessage', event);
				break;
			case SOCKET_EVENT_MARK_ROOM_AS_READ:
				const roomId = _.get(event, 'room.id');
				roomId && dispatch('markRoomAsRead', roomId);
				break;
			}
		});
	},
};

const fetchUserRooms = async ({limit, page}) => {
	const {data: response} = await axios.get(getApiUrl('chat_rooms/.getPrivateRooms'), {
		params: {
			include: 'profiles',
			limit,
			page
		}
	});

	const defaultEmptyResponse = {
		payload: {
			rooms: {},
			sortedRooms: [],
			profiles: {}
		},
		pagination: {
			hasMoreRooms: false,
			currentPage: 1
		}
	};

	if (_.isEmpty(response)) return defaultEmptyResponse;

	const {has_more, current_page, data, log_pointer} = response;

	if (_.isEmpty(data)) return defaultEmptyResponse;

	const {included = {}, ...rooms} = data;
	const payload = {
		rooms: {},
		sortedRooms: [],
		profiles: included.profiles
	};
	const pagination = {
		hasMoreRooms: has_more,
		currentPage: current_page
	};

	Object.values(rooms).forEach((room) => {
		payload.rooms[room.id] = {
			...room, messages: []
		};
		payload.sortedRooms.push(room.id);
	});
	return {payload, pagination, log_pointer};
};

const fetchPaginatedRoomMessages = async (roomId, currentCursor, limit = 10) =>  {
	const {data} = await axios.post(getApiUrl('chat_messages/.getByRooms'), {
		rooms: [roomId],
		include: 'profiles',
		limit,
		currentCursor
	});

	return serializeResponse(data, roomId);
};

const fetchRoomsMessages = async (roomsIds, limit) => {
	const {data: response} = await axios.post(getApiUrl('chat_messages/.getByRooms'), {
		rooms: roomsIds,
		limit
	});
	const rooms  = {};

	const {...roomsWithMessages} = response;
	Object.keys(roomsWithMessages).forEach(roomId => {
		rooms[roomId] = {
			messages: roomsWithMessages[roomId].data.reverse(),
			pagination: roomsWithMessages[roomId].cursor
		};
	});

	return rooms;
};

const fetchRoomMessagesWithContext = async (requestContext) => {
	const {data: response} = await axios.post(getApiUrl('chat_messages/.getWithContext'), {
		include: 'profiles',
		...requestContext
	});

	const {cursor, data: {included = {}, ...messages} = {}} = response;

	return {
		profiles: Object.values(included.profiles || {}),
		messages: Object.values(messages).reverse(),
		cursor
	};
};

const serializeResponse = (data, roomId) => {
	const roomMessages = data[roomId];

	if (!roomMessages) {
		return {
			profiles: [],
			messages: [],
			cursor: {
				next: null,
				has_more: false
			}
		};
	}

	const {data: {included = {}, ...messages}, cursor} = roomMessages;

	return {
		profiles: Object.values(included.profiles || {}),
		messages: Object.values(messages).reverse(),
		cursor
	};
};

export default {
	namespaced,
	state,
	getters,
	mutations,
	actions
};
