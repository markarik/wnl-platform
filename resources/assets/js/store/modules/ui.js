import * as types from '../mutations-types'
import { set } from 'vue'

// Initial state
const state = {
	currentLayout: '',
	isSidenavOpen: false
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
	canShowControlsInNavbar: (state, getters) => !getters.isMobile,
	isSidenavMounted: (state, getters) => !getters.canShowSidenavTrigger,
	isSidenavOpen: state => state.isSidenavOpen,
	isSidenavVisible: (state, getters) => getters.isSidenavMounted || getters.isSidenavOpen
}

// Mutations
const mutations = {
	[types.UI_CHANGE_LAYOUT] (state, layout) {
		set(state, 'currentLayout', layout)
	},
	[types.UI_TOGGLE_SIDENAV] (state) {
		set(state, 'isSidenavOpen', !state.isSidenavOpen)
	}
}

// Actions
const actions = {
	setLayout({ commit }, layout) {
		commit(types.UI_CHANGE_LAYOUT, layout)
	},
	toggleSidenav({ commit }) {
		commit(types.UI_TOGGLE_SIDENAV)
	}
}

export default {
	state,
	getters,
	mutations,
	actions
}
