import {set} from 'vue'
// import * as users from '../../api/users'
import * as types from '../mutations-types'
import * as socket from '../../socket'

// Initial state
const state = {
	currentRoom: '',
	loaded: false,
	messages: [],
	users: []
}

// Getters
const getters = {
	currentRoom: state => state.currentRoom,
	loaded: state => state.loaded,
	messages: state => state.messages
}

// Mutations
const mutations = {
	[types.CHAT_SET_ROOM] (state, room){
		set(state, 'currentRoom', room)
	},

	[types.CHAT_SET_MESSAGES] (state, messages) {
		set(state, 'messages', messages)
	},

	[types.CHAT_ADD_NEW_MESSAGE] (state, message) {
		state.thread.push(message)
	},

	[types.CHAT_TOGGLE_LOADED] (state) {
		set(state, 'loaded', !state.loaded)
	},

	[types.CHAT_SET_MESSAGES] (state, users) {
		set(state, 'users', users)
	},
}

// Actions
const actions = {
	sendMessage ({commit, state}, content) {
		this.$socket.emit('send-message', content)
	},

	chatJoinRoom ({commit}) {
		const socket = socket.getSocket()
		socket.on('connected', function (data) {
			commit(types.CHAT_SET_SOCKET, socket)
			console.log(data);
			socket.emit('join-room', 1);
		})
	}
}

export default {
	state,
	getters,
	mutations,
	actions
}
