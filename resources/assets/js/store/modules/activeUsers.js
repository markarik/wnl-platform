import {set} from 'vue'
import * as types from '../mutations-types'

// Initial state
const state = {
	activeUsers: {},
};

// Getters
const getters = {
	activeUsers: state => channel =>  state.activeUsers[channel] || [],
}

// Mutations
const mutations = {
	[types.ACTIVE_USERS_SET] (state, {users, channel}) {
		set(state, 'activeUsers', {
			...state.activeUsers,
			[channel]: users
		})
	},
}

// Actions
const actions = {
	userJoined ({commit, state}, {user, channel}) {
		console.log(channel)
		commit(types.ACTIVE_USERS_SET, {users: [user, ...state.activeUsers[channel]], channel})
	},
	userLeft({commit, state}, {user, channel}) {
		commit(types.ACTIVE_USERS_SET, {
			users: state.activeUsers[channel].filter((activeUser) => activeUser.id !== user.id),
			channel
		})
	},
	setActiveUsers({commit}, payload) {
		commit(types.ACTIVE_USERS_SET, payload)
	}
};

export default {
	state,
	getters,
	mutations,
	actions
}
