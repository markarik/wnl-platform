import axios from 'axios'
import * as types from '../mutations-types'
import { getApiUrl } from 'js/utils/env'
import { set } from 'vue'

// API functions
export function _getCurrentUser() {
	return axios.get(getApiUrl('users/current/profile'));
}

// Initial state
const state = {
	profile: {
		id: 0,
		first_name: '',
		last_name: '',
		full_name: '',
		public_email: '',
		public_phone: '',
		username: '',
		avatar: '',
	}
}

// Getters
const getters = {
	currentUser: state => state.profile,
	currentUserId: state => state.profile.id,
	currentUserAvatar: state => state.profile.avatar,
	currentUserName: state => state.profile.first_name,
	currentUserFullName: state => state.profile.full_name,
	currentUserSlug: state => state.profile.full_name.toLowerCase().replace(/\W/g, '')
}

// Mutations
const mutations = {
	[types.USERS_SETUP_CURRENT] (state, userData) {
		set(state, 'profile', userData)
	}
}

// Actions
const actions = {
	setupCurrentUser({ commit }) {
		_getCurrentUser().then((response) => {
			commit(types.USERS_SETUP_CURRENT, response.data)
		})
		.catch((error) => {
			$wnl.logger.error(error)
		})
	}
}

export default {
	state,
	getters,
	mutations,
	actions
}
