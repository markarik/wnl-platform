import * as types from '../mutations-types'
import { set } from 'vue'

// Initial state
const state = {
	currentLayout: "",
}

// Getters
const getters = {
	currentLayout: state => state.currentLayout
}

// Mutations
const mutations = {
	[types.UI_CHANGE_LAYOUT] (state, layout) {
		set(state, 'currentLayout', layout)
	}
}

// Actions
const actions = {
	setLayout({ commit }, layout) {
		commit(types.UI_CHANGE_LAYOUT, layout)
	}
}

export default {
	state,
	getters,
	mutations,
	actions
}
