import * as types from '../mutations-types'
import {getApiUrl} from 'js/utils/env'
import {getCurrentUser} from 'js/services/user';
import {set} from 'vue'

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
	currentUserId: state => state.currentUser.data.id,
	currentUserAvatar: state => state.currentUser.data.avatar,
	currentUserName: state => state.currentUser.data.first_name,
	currentUserFullName: state => state.currentUser.data.full_name,
	currentUserSlug: state => state.currentUser.data.full_name.toLowerCase().replace(/\W/g, '')
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
	setupCurrentUser({commit}) {
		getCurrentUser().then((response) => {
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
