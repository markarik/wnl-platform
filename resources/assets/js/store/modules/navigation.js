import _ from 'lodash'
import * as types from '../mutations-types'
import { set, delete as destroy } from 'vue'

// Initial state
const state = {
	breadcrumbs: [
		/**
		 * {
		 * 	text: String,
		 * 	to: Object,
		 * },
		 * ...
		 */
	],
}

// Getters
const getters = {
	breadcrumbs: state => state.breadcrumbs,
}

// Mutations
const mutations = {
	[types.ADD_BREADCRUMB] (state, payload) {
		state.breadcrumbs.push(payload)
	},
	[types.REMOVE_BREADCRUMB] (state, text) {
		let index = _.findIndex(state.breadcrumbs, ['text', text])

		if (index > -1) {
			state.breadcrumbs.splice(index, 1)
		}
	},
}

const actions = {
	addBreadcrumb({ commit }, payload) {
		commit(types.ADD_BREADCRUMB, payload)
	},
	removeBreadcrumb({ commit }, text) {
		commit(types.REMOVE_BREADCRUMB, text)
	},
}

export default {
	state,
	getters,
	mutations,
	actions,
}
