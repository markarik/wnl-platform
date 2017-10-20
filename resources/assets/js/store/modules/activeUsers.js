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
export const mutations = {
	[types.ACTIVE_USERS_SET] (state, {users, channel}) {
		set(state, 'activeUsers', {
			...state.activeUsers,
			[channel]: users
		})
	},
}

// Actions
export const actions = {
	userJoined ({commit, state}, {user, channel}) {
		const usersInChannel = state.activeUsers[channel] || [];
		commit(types.ACTIVE_USERS_SET, {users: [user, ...usersInChannel], channel})
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
