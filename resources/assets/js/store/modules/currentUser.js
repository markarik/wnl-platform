import * as types from '../mutations-types'
import { getApiUrl } from 'js/utils/env'
import { set } from 'vue'

let getCurrentUserPromise;

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
		},
		identity: {
			personalIdentityNumber: '',
			identityCardNumber: '',
			passportNumber: ''
		},
		accountSuspended: false
	},
	settings: $wnl.defaultSettings,
}

// Getters
const getters = {
	currentUser: state => state.profile,
	currentUserId: state => state.profile.user_id,
	currentUserProfileId: state => state.profile.id,
	currentUserAvatar: state => state.profile.avatar,
	currentUserEmail: state => state.profile.public_email,
	currentUserName: state => state.profile.first_name,
	currentUserFullName: state => state.profile.full_name,
	currentUserDisplayName: state => state.profile.display_name,
	currentUserRoles: state => state.profile.roles,
	currentUserSlug: state => state.profile.full_name.toLowerCase().replace(/\W/g, ''),
	currentUserIdentity: state => state.profile.identity,
	getSetting: state => setting => state.settings[setting],
	thickScrollbar: state => state.settings.thick_scrollbar,
	getAllSettings: state => state.settings,
	hasRole: state => role => state.profile.roles.indexOf(role) > -1,
	isAdmin: state => state.profile.roles.indexOf('admin') > -1,
	isModerator: state => state.profile.roles.indexOf('moderator') > -1,
	isCurrentUserLoading: state => state.loading,
	currentUserStats: state => state.stats,
	currentUserSubscriptionDates: state => state.profile.subscription.dates,
	currentUserSubscriptionActive: state => state.profile.subscription.status === 'active',
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
		set(state, 'settings', settings)
	},
	[types.USERS_CHANGE_SETTING] (state, payload) {
		set(state.settings, payload.setting, payload.value)
	},
	[types.USERS_SET_STATS] (state, payload) {
		set(state, 'stats', payload)
	},
	[types.USERS_SET_SUBSCRIPTION] (state, payload) {
		set(state, 'subscription', payload)
	},
	[types.USERS_SET_IDENTIY] (state, payload) {
		set(state.profile, 'identity', payload)
	},
	[types.USERS_SET_ACCOUNT_SUSPENDED] (state, payload) {
		set(state.profile, 'accountSuspended', payload)
	},
}

// Actions
const actions = {
	setupCurrentUser({ commit, dispatch }) {
		// Make sure that setup happens only once
		if (!getCurrentUserPromise) {
			getCurrentUserPromise = Promise
			.all([
				dispatch('fetchCurrentUserProfile'),
				dispatch('fetchUserSettings'),
			])
			.then(() => commit(types.IS_LOADING, false))
			.catch((error) => {
				$wnl.logger.error(error)
				commit(types.IS_LOADING, false)
			})
		}

		return getCurrentUserPromise
	},

	async fetchCurrentUserProfile({ commit }) {
		let userResponse, subscriptionResponse;

		try {
			[userResponse, subscriptionResponse] = await Promise.all([
				axios.get(getApiUrl('users/current/profile?include=roles')),
				axios.get(getApiUrl('user_subscription/current'))
			])
		} catch (error) {
			$wnl.logger.error(error)
			throw error;
		}

		const {included, ...profile} = userResponse.data;
		if (!included) {
			profile.roles = [];
		} else {
			const roles = included.roles || {}
			profile.roles = Object.values(roles)
				.map(role => role.name)
		}

		const currentUser = {
			...profile,
			subscription: subscriptionResponse.data
		}

		if (!currentUser.user_id) {
			$wnl.logger.error('current user returned user with ID 0', {
				profile: currentUser
			})
			throw new Error('current user returned user with ID 0');
		}
		commit(types.USERS_SETUP_CURRENT, currentUser)
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

	async fetchUserSettings({ commit }) {
		try {
			const { data } = await axios.get(getApiUrl('users/current/settings'));
			commit(types.USERS_SETUP_SETTINGS, data)
		} catch (error) {
			$wnl.logger.error(error)
			throw error
		}
	},

	async fetchUserPersonalData({ commit }) {
		try {
			const response = await axios.get(getApiUrl(`users/current/personal_data`))
			commit(types.USERS_SET_IDENTIY, response.data)
		}
		catch (error) {
			const emptyResponse = {
				personalIdentityNumber: null,
				identityCardNumber: null,
				passportNumber: null
			}
			if (error.response.status === 404) {
				commit(types.USERS_SET_IDENTIY, emptyResponse)
			}
			commit(types.USERS_SET_IDENTIY, emptyResponse)
			$wnl.logger.error(error)
		}
	},

	updateCurrentUser({commit}, userData) {
		commit(types.USERS_UPDATE_CURRENT, userData)
	},

	changeUserSetting({ commit }, payload) {
		commit(types.USERS_CHANGE_SETTING, payload)
	},

	setUserIdentity({ commit }, payload) {
		commit(types.USERS_SET_IDENTIY, payload)
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
		return axios.put(getApiUrl('users/current/settings'), getters.getAllSettings)
	},

	deleteAccount({getters}, payload) {
		return axios.patch(getApiUrl(`users/${getters.currentUserId}/forget`), {
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
