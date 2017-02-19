import {set} from 'vue'
// import * as users from '../../api/users'
import * as types from '../mutations-types'
import * as io from 'socket.io-client'

// Initial state
const state = {
	currentRoom: '',
	thread: [],
	socket: {}
}

// Getters
const getters = {
	currentRoom: state => state.currentRoom,
	socket: state => state.socket
}

// Mutations
const mutations = {
	[types.CHAT_SET_ROOM] (state, room){
		set(state, 'currentRoom', room)
	},

	[types.CHAT_SET_MESSAGES] (state, messages) {
		set(state, 'messages', messages)
	},

	[types.CHAT_ADD_MESSAGE] (state, message) {
		state.thread.push(message)
	},

	[types.CHAT_SET_SOCKET] (state, socket) {
		set(state, 'socket', socket)
	}
}

// Actions
const actions = {
	sendMessage ({commit, state}, content) {
		this.$socket.emit('send-message', 'dupa')
	},

	chatJoinRoom ({commit}) {
		// var socket = io('46.101.174.6:9663')
		// socket.on('connected', function (data) {
		// 	commit(types.CHAT_SET_SOCKET, socket)
		// 	console.log(data);
		// 	socket.emit('join-room', 1);
		// })
	}
}

export default {
	state,
	getters,
	mutations,
	actions
}
