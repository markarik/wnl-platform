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

function getUserSearchConditions(data) {
	const where = [
		['first_name', 'like', `%${data.firstName}%`]
	]
	data.lastName && where.push(
		['last_name', 'like', `%${data.lastName}%`]
	)

	return { query: { where }, limit: [5, 0] }
}

function getTagSearchConditions(data) {
	const where = [
		['name', 'like', `%${data.name}%`]
	]

	return { query: { where }, limit: [5, 0] }
}

// Actions
const actions = {
	requestUsersAutocomplete({}, data) {
		const conditions = getUserSearchConditions(data)

		return axios.post(getApiUrl('user_profiles/.search'), conditions)
	},
	requestTagsAutocomplete({}, data) {
		const conditions = getTagSearchConditions(data)

		return axios.post(getApiUrl('tags/.search'), conditions)
	}
};

export default {
	state,
	getters,
	mutations,
	actions
}
