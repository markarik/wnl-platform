import * as types from 'js/store/mutations-types';
import { set, delete as destroy } from 'vue';
import { isString, pickBy, values } from 'lodash';
import {SETTING_NAMES} from 'js/consts/settings';

// Initial state
const state = {
	canShowChat: false,
	currentLayout: '',
	isSidenavOpen: false,
	isChatOpen: false,
	navigationToggleState: {},
	overlays: {},
	overviewView: 'stream',
	modalVisible: false
};

const layouts = {
	mobile: 'mobile',
	tablet: 'tablet',
	smallDesktop: 'small_desktop',
	largeDesktop: 'large_desktop'
};

// Getters
const getters = {
	currentLayout: state => state.currentLayout,
	isMobile: state => state.currentLayout === layouts.mobile,
	isSmallDesktop: state => state.currentLayout === layouts.smallDesktop,
	isLargeDesktop: state => state.currentLayout === layouts.largeDesktop,
	isTouchScreen: state =>
		[layouts.mobile, layouts.tablet].indexOf(state.currentLayout) !== -1,
	canShowSidenavTrigger: (state, getters) => getters.isTouchScreen,
	canShowControlsInNavbar: (state, getters) => !getters.isMobile,
	canShowChatToggleInNavbar: (state, getters, rootState) => {
		// TODO: Figure out how to hide chat in a smarter way
		return state.canShowChat && getters.isMobile && rootState.route.path.indexOf('myself') === -1;
	},
	isSidenavMounted: (state, getters) => !getters.canShowSidenavTrigger,
	isSidenavOpen: state => state.isSidenavOpen,
	isSidenavVisible: (state, getters) => getters.isSidenavMounted || getters.isSidenavOpen,
	isMobileNavigation: (state, getters) => getters.isTouchScreen,
	isMobileProfile: (state, getters) => getters.isTouchScreen,
	isChatMounted: (state, getters) => getters.isLargeDesktop,
	isChatVisible: (state, getters, rootState) => state.canShowChat && getters.isChatMounted ?
		rootState.currentUser.settings[SETTING_NAMES.CHAT_ON] : state.isChatOpen,
	isChatToggleVisible: (state, getters) => !getters.isMobile && !getters.isChatVisible,
	canShowCloseIconInChat: (state, getters) => !getters.isMobile,
	canShowChat: state => state.canShowChat,
	isOverlayVisible: state => _.size(state.overlays) > 0,
	shouldDisplayOverlay: state => Object.keys(state.overlays).length > 0,
	isNavigationGroupExpanded: state => groupId => state.navigationToggleState[groupId],
	overviewView: state => state.overviewView,
	overlayTexts: state => values(pickBy(state.overlays, isString)),
	modalVisible: state => state.modalVisible
};

// Mutations
const mutations = {
	[types.UI_CHANGE_LAYOUT] (state, layout) {
		set(state, 'currentLayout', layout);
	},
	[types.UI_TOGGLE_SIDENAV] (state) {
		const isSidenavOpen = state.isSidenavOpen;

		if (!isSidenavOpen) {
			set(state, 'isChatOpen', false);
		}

		set(state, 'isSidenavOpen', !state.isSidenavOpen);
	},
	[types.UI_RESET_LAYOUT] (state) {
		set(state, 'isSidenavOpen', false);
	},
	[types.UI_TOGGLE_CHAT] (state) {
		const isChatOpen = state.isChatOpen;

		if (!isChatOpen) {
			set(state, 'isSidenavOpen', false);
		}

		set(state, 'isChatOpen', !state.isChatOpen);
	},
	[types.UI_SET_CHAT_OPEN] (state, isChatOpen) {
		set(state, 'isChatOpen', isChatOpen);
	},
	[types.UI_CLOSE_SIDENAVS] (state) {
		set(state, 'isChatOpen', false);
		set(state, 'isSidenavOpen', false);
	},
	[types.UI_INIT_CHAT] (state) {
		set(state, 'canShowChat', true);
	},
	[types.UI_KILL_CHAT] (state) {
		set(state, 'canShowChat', false);
	},
	[types.UI_DISPLAY_OVERLAY] (state, payload) {
		if (payload.display) {
			set(state.overlays, payload.source, payload.text || true);
		} else {
			destroy(state.overlays, payload.source);
		}
	},
	[types.UI_TOGGLE_NAVIGATION_GROUP] (state, {groupIndex, isOpen}) {
		set(state.navigationToggleState, groupIndex, isOpen);
	},
	[types.UI_CHANGE_OVERVIEW_VIEW] (state, view) {
		set(state, 'overviewView', view);
	}
};

// Actions
const actions = {
	setLayout({ commit, getters }, layout) {
		commit(types.UI_CHANGE_LAYOUT, layout);
	},
	toggleSidenav({ commit }) {
		commit(types.UI_TOGGLE_SIDENAV);
	},
	resetLayout({ commit }) {
		commit(types.UI_RESET_LAYOUT);
	},
	toggleChat({ commit }) {
		commit(types.UI_TOGGLE_CHAT);
	},
	closeSidenavs({ commit }) {
		commit(types.UI_CLOSE_SIDENAVS);
	},
	initChat({ commit }) {
		commit(types.UI_INIT_CHAT);
	},
	killChat({ commit }) {
		commit(types.UI_KILL_CHAT);
	},
	toggleOverlay({ commit }, payload) {
		commit(types.UI_DISPLAY_OVERLAY, payload);
	},
	toggleNavigationGroup({commit}, payload) {
		commit(types.UI_TOGGLE_NAVIGATION_GROUP, payload);
	},
	changeOverviewView({commit}, view) {
		commit(types.UI_CHANGE_OVERVIEW_VIEW, view);
	},
	showNotification({commit}, {type = 'success', message, timeout = 3000}) {
		commit(types.UI_SHOW_GLOBAL_NOTIFICATION, {type, message});

		setTimeout(() => {
			commit(types.UI_SHOW_GLOBAL_NOTIFICATION, false);
		}, timeout);
	},
	[types.SOCKET_CONNECTION_ERROR]({commit}) {
		commit(`chatMessages/${types.CHAT_MESSAGES_SET_STATUS}`, false);
	},
	[types.SOCKET_CONNECTION_RECONNECTED]({commit}) {
		commit(`chatMessages/${types.CHAT_MESSAGES_SET_STATUS}`, true);
	},
};

export default {
	state,
	getters,
	mutations,
	actions
};
