import {set} from 'vue'
import * as types from '../mutations-types'

// Initial state
const state = {
	activeUsers: [],
};

// Getters
const getters = {
	activeUsers: state => state.activeUsers,
}

// Mutations
const mutations = {
	[types.ACTIVE_USERS_SET] (state, activeUsers) {
		set(state, 'activeUsers', activeUsers)
	},
}

// Actions
const actions = {
	userJoined ({commit, state}, user) {
		commit(types.ACTIVE_USERS_SET, [user, ...state.activeUsers])
	},
	userLeft({commit, state}, user) {
		commit(types.ACTIVE_USERS_SET, state.activeUsers.filter((activeUser) => activeUser.id !== user.id))
	},
	setActiveUsers({commit}, users) {
		commit(types.ACTIVE_USERS_SET, users)
	}
};

export default {
	state,
	getters,
	mutations,
	actions
}
