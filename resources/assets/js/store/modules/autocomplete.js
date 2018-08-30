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
	let displayName = data.firstName
	if(data.lastName) displayName += ` ${data.lastName}`
	const orWhere = [
		['display_name', 'like', `%${displayName}%`]
	]
	return { query: { where, orWhere, whereNull: ['deleted_at'] }, limit: [5, 0] }
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
		const conditions = getUserSearchConditions(data)

		return axios.post(getApiUrl('user_profiles/.query'), conditions)
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
