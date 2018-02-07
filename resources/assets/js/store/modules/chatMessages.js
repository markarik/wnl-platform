import {set} from 'vue'
import * as types from '../mutations-types'
import * as socket from '../../socket'
import {getApiUrl} from 'js/utils/env'

const namespaced = true

//Initial state
const state = {
	rooms: {
	},
	sortedRooms: []
}

//Getters
const getters = {
	rooms: state => state.rooms,
	sortedRooms: state => state.sortedRooms
}

//mutations
const mutations = {
	[types.SET_MESSAGES] (state, data) {
		set (state, 'rooms', data.rooms)
		set (state, 'sortedRooms', data.sortedRooms)
	}
}

//Actions
const actions = {
	fetchInitialState({commit}) {
		axios.get(getApiUrl('chat_rooms/.getPrivateRooms')).then((response) => {
			const data = response.data
			const formatedRooms = {
				rooms: {},
				sortedRooms: []
			}
			data.forEach((room) => {
				formatedRooms.rooms[room.id] = room
				formatedRooms.sortedRooms.push(room.id)
			})
			commit(types.SET_MESSAGES, formatedRooms)
		})

		axios.post(getApiUrl('chat_messages/.getByRooms')).then((response) => {
			console.log(response);
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
