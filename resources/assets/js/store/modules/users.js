import axios from 'axios'
import * as types from '../mutations-types'
import { getApiUrl } from 'js/utils/env'
import { set } from 'vue'

// API functions
export function getCurrent() {
	return axios.get(getApiUrl('users/current'));
}

// Initial state
const state = {
	currentUser: {
		data: {
			id: 0,
			first_name: '',
			last_name: '',
			full_name: ''
		}
	}
}

// Getters
const getters = {
	currentUser: state => state.currentUser,
	currentUserFullName: state => state.currentUser.data.full_name,
}

// Mutations
const mutations = {
	[types.USERS_SETUP_CURRENT] (state, userData) {
		userData['full_name'] = `${userData['first_name']} ${userData['last_name']}`
		set(state.currentUser, 'data', userData)
	}
}

// Actions
const actions = {
	setupCurrentUser({ commit }) {
		getCurrent().then((response) => {
			commit(types.USERS_SETUP_CURRENT, response.data)
		})
	}
}

export default {
	state,
	getters,
	mutations,
	actions
}
