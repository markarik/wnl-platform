import { set } from 'vue'
import * as users from '../../api/users'
import * as types from '../mutations-types'

// Initial state
const state = {
	current: {
		id: 0,
		first_name: '',
		last_name: '',
		full_name: ''
	}
}

// Getters
const getters = {
	current: state => state.current,
	currentUserFullName: state => state.current.full_name
}

// Mutations
const mutations = {
	[types.SET_CURRENT_USER] (state, userData) {
		userData['full_name'] = userData['first_name']  + ' ' + userData['last_name']
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
