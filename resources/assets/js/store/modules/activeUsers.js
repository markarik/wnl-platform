import {set} from 'vue'
import * as types from '../mutations-types'

// Initial state
const state = {};

// Getters
const getters = {
	activeUsers: state => channel =>  state[channel] || [],
}

// Mutations
export const mutations = {
	[types.ACTIVE_USERS_SET] (state, {users, channel}) {
		set(state, channel, users)
	},
}

// Actions
export const actions = {
	userJoined ({commit, state}, {user, channel}) {
		const usersInChannel = state[channel] || [];

		commit(types.ACTIVE_USERS_SET, {users: [user, ...usersInChannel], channel})
	},
	userLeft({commit, state}, {user, channel}) {
		commit(types.ACTIVE_USERS_SET, {
			users: state[channel].filter((activeUser) => activeUser.id !== user.id),
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
