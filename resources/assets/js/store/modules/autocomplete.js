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

function getTagSearchConditions(name, tags = []) {
	const where = [
		['name', 'like', `%${name}%`]
	]

	const whereNotIn = ['id',  tags.map(tag => tag.id)]

	return { query: { where, whereNotIn }, limit: [5, 0] }
}

// Actions
const actions = {
	requestUsersAutocomplete({}, data) {
		let query = Object.values(data).join(' ')
		return axios.get(getApiUrl(`user_profiles/.search?q=${query}`))
	},
	requestTagsAutocomplete({}, { name, tags }) {
		const conditions = getTagSearchConditions(name, tags)

		return axios.post(getApiUrl('tags/.search'), conditions)
	}
};

export default {
	state,
	getters,
	mutations,
	actions
}
