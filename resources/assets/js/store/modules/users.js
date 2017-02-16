import { set } from 'vue'
import * as users from '../../api/users'
import * as types from '../mutations-types'

// Initial state
const state = {
	current: {}
}

// Getters
const getters = {
	current: state => state.current
}

// Mutations
const mutations = {
	[types.SET_CURRENT_USER] (state, userData) {
		set(state, 'current', userData)
	}
}

// Actions
const actions = {
	setCurrentUser({ commit }) {
		users.getCurrent().then((response) => {
			commit(types.SET_CURRENT_USER, response.data)
		})
	}
}

export default {
	state,
	getters,
	mutations,
	actions
}
