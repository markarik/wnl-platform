import * as types from '../mutations-types'
import { set, delete as destroy } from 'vue'

// Initial state
const state = {
	currentLayout: '',
	isSidenavOpen: false,
	isChatOpen: false,
	isOverlayVisible: false,
	overlays: {},
	canShowChat: false
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
	isLargeDesktop: state => state.currentLayout === layouts.largeDesktop,
	isTouchScreen: state =>
		[layouts.mobile, layouts.tablet].indexOf(state.currentLayout) !== -1,
	canShowSidenavTrigger: (state, getters) => getters.isTouchScreen,
	canShowBreadcrumbsInNavbar: (state, getters) => !getters.isTouchScreen,
	canShowControlsInNavbar: (state, getters) => !getters.isMobile,
	canShowChatToggleInNavbar: (state, getters, rootState) => {
		// TODO: Figure out how to hide chat in a smarter way
		return state.canShowChat && getters.isMobile && rootState.route.path.indexOf('myself') === -1
	},
	isSidenavMounted: (state, getters) => !getters.canShowSidenavTrigger,
	isSidenavOpen: state => state.isSidenavOpen,
	isSidenavVisible: (state, getters) => getters.isSidenavMounted || getters.isSidenavOpen,
	isMobileNavigation: (state, getters) => getters.isTouchScreen,
	isMobileProfile: (state, getters) => getters.isTouchScreen,
	isMobileCollections: (state, getters) => getters.isTouchScreen,
	isChatMounted: (state, getters) => getters.isLargeDesktop,
	isChatVisible: (state, getters, rootState) => state.canShowChat && getters.isChatMounted ?
		rootState.currentUser.settings["chat_on"] : state.isChatOpen,
	isChatToggleVisible: (state, getters) => !getters.isMobile && !getters.isChatVisible,
	canShowCloseIconInChat: (state, getters) => !getters.isMobile,
	canShowChat: state => state.canShowChat,
	isOverlayVisible: state => state.isOverlayVisible,
	shouldDisplayOverlay: state => Object.keys(state.overlays).length > 0
}

// Mutations
const mutations = {
	[types.UI_CHANGE_LAYOUT] (state, layout) {
		set(state, 'currentLayout', layout)
	},
	[types.UI_TOGGLE_SIDENAV] (state) {
		const isSidenavOpen = state.isSidenavOpen

		if (!isSidenavOpen) {
			set(state, 'isChatOpen', false);
		}

		set(state, 'isSidenavOpen', !state.isSidenavOpen)
	},
	[types.UI_RESET_LAYOUT] (state) {
		set(state, 'isSidenavOpen', false)
	},
	[types.UI_TOGGLE_CHAT] (state) {
		const isChatOpen = state.isChatOpen

		if (!isChatOpen) {
			set(state, 'isSidenavOpen', false);
		}

		set(state, 'isChatOpen', !state.isChatOpen)
	},
	[types.UI_SET_CHAT_OPEN] (state, isChatOpen) {
		set(state, 'isChatOpen', isChatOpen)
	},
	[types.UI_CLOSE_SIDENAVS] (state) {
		set(state, 'isChatOpen', false)
		set(state, 'isSidenavOpen', false)
	},
	[types.UI_INIT_CHAT] (state) {
		set(state, 'canShowChat', true)
	},
	[types.UI_KILL_CHAT] (state) {
		set(state, 'canShowChat', false)
	},
	[types.UI_TOGGLE_OVERLAY] (state, isVisible) {
		set(state, 'isOverlayVisible', isVisible)
	},
	[types.UI_DISPLAY_OVERLAY] (state, payload) {
		if (payload.display) {
			set(state.overlays, payload.source, true)
		} else {
			destroy(state.overlays, payload.source)
		}
	},
}

// Actions
const actions = {
	setLayout({ commit, getters }, layout) {
		commit(types.UI_CHANGE_LAYOUT, layout)
	},
	toggleSidenav({ commit }) {
		commit(types.UI_TOGGLE_SIDENAV)
	},
	resetLayout({ commit, getters }) {
		commit(types.UI_RESET_LAYOUT)
	},
	toggleChat({ commit }) {
		commit(types.UI_TOGGLE_CHAT)
	},
	closeSidenavs({ commit }) {
		commit(types.UI_CLOSE_SIDENAVS)
	},
	initChat({ commit }) {
		commit(types.UI_INIT_CHAT)
	},
	killChat({ commit }) {
		commit(types.UI_KILL_CHAT)
	},
	toggleOverlay({ commit }, payload) {
		commit(types.UI_DISPLAY_OVERLAY, payload)
	}
}

export default {
	state,
	getters,
	mutations,
	actions
}
