import {set} from 'vue'
import * as types from '../mutations-types'

// Initial state
const state = {
	activeUsers: {},
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
		$wnl.logger.debug('user joined...')
		commit(types.ACTIVE_USERS_SET, {...state.activeUsers, [user.id]: user})
	},
	userLeft({commit, state}, user) {
		$wnl.logger.debug('user left...')
		commit(types.ACTIVE_USERS_SET, _.omit(state.activeUsers, user.id))
	},
	setActiveUsers({commit}, users) {
		const activeUsers = Object.assign({}, ...users.map(user => ({ [user.id]: user })));
		commit(types.ACTIVE_USERS_SET, activeUsers)
	}
};

export default {
	state,
	getters,
	mutations,
	actions
}
