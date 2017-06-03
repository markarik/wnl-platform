import * as types from '../mutations-types'
import { set } from 'vue'

// Initial state
const state = {
	currentLayout: ''
}

const layouts = {
	mobile: 'mobile',
	tablet: 'tablet',
	smallDesktop: 'small_screen',
	largeDesktop: 'large_desktop'
}

// Getters
const getters = {
	currentLayout: state => state.currentLayout,
	isMobile: state => state.currentLayout === layouts.mobile,
	isTouchScreen: state =>
		[layouts.mobile, layouts.tablet].indexOf(state.currentLayout) !== -1,
	canShowSidenavTrigger: (state, getters) => getters.isTouchScreen,
	canShowBreadcrumbsInNavbar: (state, getters) => !getters.isTouchScreen,
	canShowControlsInNavbar: (state, getters) => !getters.isMobile
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
