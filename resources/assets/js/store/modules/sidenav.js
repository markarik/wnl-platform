import { set } from 'vue'
import * as sidenav from '../../api/sidenav'
import * as types from '../mutations-types'

// Initial state
const state = {
	navigation: {
		breadcrumbs: [],
		items: []
	}
}

// Getters
const getters = {
	breadcrumbs: state => state.navigation.breadcrumbs,
	items: state => state.navigation.items
}

// Mutations
const mutations = {
	[types.SET_NAVIGATION] (state, navigationData) {
		set(state, 'navigation', navigationData)
	}
}

// Actions
const actions = {
	setNavigation({ commit }, url) {
		console.log('url')
		sidenav.getNavigation(url).then((response) => {
			commit(types.SET_NAVIGATION, response.data)
		})
	}
}

export default {
	state,
	getters,
	mutations,
	actions
}
