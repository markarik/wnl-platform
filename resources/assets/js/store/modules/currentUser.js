import * as types from '../mutations-types'
import { getApiUrl } from 'js/utils/env'
import { getCurrentUser, getUserSettings, getDefaultSettings, setUserSettings } from 'js/services/user';
import { set } from 'vue'

// Initial state
const state = {
	loading: true,
	profile: {
		id: 0,
		first_name: '',
		last_name: '',
		full_name: '',
		public_email: '',
		public_phone: '',
		display_name: '',
		username: '',
		avatar: '',
		roles: [],
		user_id: 0,
		subscription: {
			dates: {
				min: 0, max: 0
			}
		}
	},
	settings: getDefaultSettings(),
}

// Getters
const getters = {
	currentUser: state => state.profile,
	currentUserId: state => state.profile.user_id,
	currentUserAvatar: state => state.profile.avatar,
	currentUserEmail: state => state.profile.public_email,
	currentUserName: state => state.profile.first_name,
	currentUserFullName: state => state.profile.full_name,
	currentUserDisplayName: state => state.profile.display_name,
	currentUserRoles: state => state.profile.roles,
	currentUserSlug: state => state.profile.full_name.toLowerCase().replace(/\W/g, ''),
	getSetting: state => setting => state.settings[setting],
	getAllSettings: state => state.settings,
	hasRole: state => role => state.profile.roles.indexOf(role) > -1,
	isAdmin: state => state.profile.roles.indexOf('admin') > -1,
	isModerator: state => state.profile.roles.indexOf('moderator') > -1,
	isCurrentUserLoading: state => state.loading,
	currentUserStats: state => state.stats,
	currentUserSubscriptionDates: state => state.profile.subscription.dates,
}

// Mutations
const mutations = {
	[types.IS_LOADING] (state, isLoading) {
		set(state, 'loading', isLoading)
	},
	[types.USERS_SETUP_CURRENT] (state, userData) {
		set(state, 'profile', userData)
	},
	[types.USERS_UPDATE_CURRENT] (state, userData) {
		Object.keys(userData).forEach((key) => {
			set(state.profile, key, userData[key])
		})
	},
	[types.USERS_SETUP_SETTINGS] (state, settings) {
		console.log(settings, '~~~~~~~~~~~~~~~~~~~~~settings');
		set(state, 'settings', settings)
	},
	[types.USERS_CHANGE_SETTING] (state, payload) {
		console.log(payload, '££££££££££££££££££££££££££££££USERS_CHANGE_SETTING');
		set(state.settings, payload.setting, payload.value)
	},
	[types.USERS_SET_STATS] (state, payload) {
		set(state, 'stats', payload)
	},
	[types.USERS_SET_SUBSCRIPTION] (state, payload) {
		set(state, 'subscription', payload)
	}
}

// Actions
const actions = {
	setupCurrentUser({ commit, dispatch }) {
		return Promise
			.all([
				dispatch('fetchCurrentUserProfile'),
				dispatch('fetchUserSettings'),
			])
			.then(() => commit(types.IS_LOADING, false))
			.catch((error) => {
				$wnl.logger.error(error)
				commit(types.IS_LOADING, false)
			})
	},

	fetchCurrentUserProfile({ commit }) {
		return new Promise((resolve, reject) => {
			getCurrentUser().then((user) => {
				if (!user.user_id) {
					$wnl.logger.error('current user returned user with ID 0', {
						profile: user
					})
					return reject(new Error('current user returned user with ID 0'));
				}
				commit(types.USERS_SETUP_CURRENT, user)
				resolve()
			})
			.catch((error) => {
				$wnl.logger.error(error)
				reject(error)
			})
		})
	},

	fetchCurrentUserStats({commit, getters}) {
		return new Promise((resolve, reject) => {
			_fetchUserStats(getters.currentUserId)
			.then(({data}) => {
				commit(types.USERS_SET_STATS, data)
				resolve()
			})
			.catch((error) => {
				$wnl.logger.error(error)
				reject()
			})
		})
	},

	fetchUserSettings({ commit }) {
		return new Promise((resolve, reject) => {
			getUserSettings().then((response) => {
				commit(types.USERS_SETUP_SETTINGS, response.data)
				resolve()
			})
			.catch((error) => {
				$wnl.logger.error(error)
				reject()
			})
		})
	},

	updateCurrentUser({commit}, userData) {
		commit(types.USERS_UPDATE_CURRENT, userData)
	},

	changeUserSetting({ commit }, payload) {
		commit(types.USERS_CHANGE_SETTING, payload)
	},

	changeUserSettingAndSync({ commit, dispatch }, payload) {
		dispatch("changeUserSetting", payload)
		dispatch("syncSettings")
	},

	toggleChat({ dispatch, getters }) {
		dispatch("changeUserSettingAndSync", {
			setting: "chat_on", value: !getters.getSetting("chat_on")
		})
	},

	syncSettings({ commit, getters }) {
		setUserSettings(getters.getAllSettings)
	},

	deleteAccount({getters}, payload) {
		return axios.patch(getApiUrl(`user_forget/${getters.currentUserId}`), {
			password: payload
		})
	}
}

const _fetchUserStats = (userId) => {
	return axios.get(getApiUrl(`users/${userId}/state/stats`));
}

export default {
	state,
	getters,
	mutations,
	actions
}
