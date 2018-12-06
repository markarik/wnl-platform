import {set} from 'vue'
import * as types from '../mutations-types'
import {getApiUrl} from 'js/utils/env'

// Initial state
const state = {
	lastSearch: null,
};

// Getters
const getters = {
}

// Mutations
const mutations = {
	[types.GET_USERS_AUTOCOMPLETE] (state, activeUsers) {
		set(state, 'lastSearch', activeUsers)
	},
}

// Actions
const actions = {
	requestUsersAutocomplete({}, data) {
		let query = Object.values(data).join(' ')
		return axios.get(getApiUrl(`user_profiles/.search?q=${query}`))
	},
	requestTagsAutocomplete({}, { name, tags }) {
		return axios.post(getApiUrl('tags/matchingName'), {
			name,
			excludedIds: tags.map(({id}) => id)
		})
	}
};

export default {
	state,
	getters,
	mutations,
	actions
}
