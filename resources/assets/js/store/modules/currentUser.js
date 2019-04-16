import axios from 'axios';
import { set } from 'vue';

import * as types from 'js/store/mutations-types';
import { getApiUrl } from 'js/utils/env';
import { USER_SETTING_NAMES } from 'js/consts/settings';
import { ONBOARDING_STEPS, ROLES, SUBSCRIPTION_STATUS } from 'js/consts/user';

let getCurrentUserPromise;

// Initial state
const state = {
	loading: true,
	loadingError: false,
	id: 0,
	profile: {
		id: 0,
		user_id: 0,
		first_name: '',
		last_name: '',
		full_name: '',
		public_email: '',
		public_phone: '',
		username: '',
		avatar: '',
		identity: {
			personalIdentityNumber: '',
			identityCardNumber: '',
			passportNumber: ''
		},
	},
	hasFinishedEntryExam: false,
	accountSuspended: false,
	roles: [],
	subscription: {
		dates: {
			min: 0, max: 0
		}
	},
	latestProductState: null,
	settings: $wnl.defaultSettings,
};

// Getters
const getters = {
	isCurrentUserLoading: state => state.loading,

	currentUser: state => state,
	currentUserId: state => state.id,
	currentUserHasFinishedEntryExam: state => state.hasFinishedEntryExam,

	currentUserProfile: state => state.profile,
	currentUserProfileId: state => state.profile.id,
	currentUserAvatar: state => state.profile.avatar,
	currentUserEmail: state => state.profile.public_email,
	currentUserName: state => state.profile.first_name,
	currentUserFullName: state => state.profile.full_name,
	currentUserIdentity: state => state.profile.identity,

	currentUserRoles: state => state.roles,
	isAdmin: state => state.roles.indexOf(ROLES.ADMIN) > -1,
	isModerator: state => state.roles.indexOf(ROLES.MODERATOR) > -1,

	currentUserLastestProductState: state => state.latestProductState,
	isOnboardingFinished: (state, getters) => state.latestProductState && state.latestProductState.onboarding_step === ONBOARDING_STEPS.FINISHED || getters.isAdmin,

	currentUserAccountSuspended: state => state.accountSuspended,
	currentUserStats: state => state.stats,

	getSetting: state => setting => state.settings[setting],
	thickScrollbar: state => state.settings[USER_SETTING_NAMES.THICK_SCROLLBAR],
	getAllSettings: state => state.settings,

	currentUserSubscriptionDates: state => state.subscription && state.subscription.subscription_dates,
	currentUserSubscriptionStatus: state => state.subscription && state.subscription.subscription_status,
	currentUserSubscriptionActive: state => state.subscription && state.subscription.subscription_status === SUBSCRIPTION_STATUS.ACTIVE,
	currentUserHasLatestProduct: state => state.hasLatestCourseProduct && state.hasLatestCourseProduct.has_latest_course_product,
	currentUserLoadingError: state => state.loadingError,
};

// Mutations
const mutations = {
	[types.IS_LOADING] (state, isLoading) {
		set(state, 'loading', isLoading);
	},
	[types.USERS_UPDATE_CURRENT] (state, userData) {
		Object.keys(userData).forEach((key) => {
			set(state, key, userData[key]);
		});
	},
	[types.USERS_CHANGE_SETTING] (state, payload) {
		set(state.settings, payload.setting, payload.value);
	},
	[types.USERS_SET_STATS] (state, payload) {
		set(state, 'stats', payload);
	},
	[types.USERS_SET_SUBSCRIPTION] (state, payload) {
		set(state, 'subscription', payload);
	},
	[types.USERS_SET_IDENTITY] (state, payload) {
		set(state.profile, 'identity', payload);
	},
	[types.USERS_SET_ACCOUNT_SUSPENDED] (state, payload) {
		set(state, 'accountSuspended', payload);
	},
	[types.USERS_SET_LOADING_ERROR] (state, payload) {
		set(state, 'loadingError', payload);
	},
};

// Actions
const actions = {
	setupCurrentUser({ commit, dispatch }) {
		// Make sure that setup happens only once
		if (!getCurrentUserPromise) {
			getCurrentUserPromise = Promise
				.resolve(dispatch('fetchCurrentUser'))
				.catch((error) => {
					$wnl.logger.error(error);
					commit(types.USERS_SET_LOADING_ERROR, true);
				})
				.finally(() => commit(types.IS_LOADING, false));
		}

		return getCurrentUserPromise;
	},

	async fetchCurrentUser({ commit }) {
		let response;

		try {
			response = await axios.get(
				getApiUrl('users/current?include=roles,profile,subscription,settings,latest_product_state,has_latest_course_product')
			);
		} catch (error) {
			$wnl.logger.error(error);
			throw error;
		}

		const {
			id,
			has_finished_entry_exam,
			has_latest_course_product,
			suspended,
			profile,
			subscription,
			settings,
			latest_product_state,
			included
		} = response.data;

		const currentUser = {
			id,
			hasFinishedEntryExam: has_finished_entry_exam,
		};

		if (included.roles) {
			currentUser.roles = Object.values(included.roles).map(role => role.name);
		}

		if (profile) {
			currentUser.profile = included.profiles[profile[0]];
		}

		if (subscription) {
			currentUser.subscription = included.subscriptions[subscription[0]];
		}

		if (latest_product_state) {
			currentUser.latestProductState = included.latest_product_states[latest_product_state[0]];
		}

		if (settings) {
			currentUser.settings = included.settings[settings[0]];
		}

		if (has_latest_course_product) {
			currentUser.hasLatestCourseProduct = included.has_latest_course_products[has_latest_course_product[0]];
		}

		if (!id) {
			$wnl.logger.error('current user returned user with ID 0', {
				profile,
			});
			throw new Error('current user returned user with ID 0');
		}
		commit(types.USERS_UPDATE_CURRENT, currentUser);

		if (suspended) {
			commit(types.USERS_SET_ACCOUNT_SUSPENDED, true);
		}
	},

	async fetchUserSubscription({ commit }) {
		try {
			const response = await axios.get(getApiUrl('user_subscription/current'));
			commit(types.USERS_SET_SUBSCRIPTION, response.data);
		} catch (e) {
			$wnl.logger.capture(e);
		}
	},

	fetchCurrentUserStats({ commit, getters }) {
		return new Promise((resolve, reject) => {
			_fetchUserStats(getters.currentUserId)
				.then(({ data }) => {
					commit(types.USERS_SET_STATS, data);
					resolve();
				})
				.catch((error) => {
					$wnl.logger.error(error);
					reject();
				});
		});
	},

	async fetchUserPersonalData({ commit }) {
		try {
			const response = await axios.get(getApiUrl('users/current/personal_data'));
			commit(types.USERS_SET_IDENTITY, response.data);
		} catch (error) {
			const emptyResponse = {
				personalIdentityNumber: null,
				identityCardNumber: null,
				passportNumber: null
			};
			commit(types.USERS_SET_IDENTITY, emptyResponse);
			$wnl.logger.error(error);
		}
	},

	updateCurrentUser({ commit }, userData) {
		commit(types.USERS_UPDATE_CURRENT, userData);
	},

	changeUserSetting({ commit }, payload) {
		commit(types.USERS_CHANGE_SETTING, payload);
	},

	setUserIdentity({ commit }, payload) {
		commit(types.USERS_SET_IDENTITY, payload);
	},

	changeUserSettingAndSync({ dispatch }, payload) {
		dispatch('changeUserSetting', payload);
		dispatch('syncSettings');
	},

	toggleChat({ dispatch, getters }) {
		dispatch('changeUserSettingAndSync', {
			setting: USER_SETTING_NAMES.CHAT_ON, value: !getters.getSetting(USER_SETTING_NAMES.CHAT_ON)
		});
	},

	syncSettings({ getters }) {
		return axios.put(getApiUrl('users/current/settings'), getters.getAllSettings);
	},

	deleteAccount({ getters }, payload) {
		return axios.patch(getApiUrl(`users/${getters.currentUserId}/forget`), {
			password: payload
		});
	},

	updateLatestProductState({ commit, getters }, payload) {
		return axios.put(getApiUrl(`users/${getters.currentUserId}/user_product_state/latest`), payload)
			.then(() => {
				commit(types.USERS_UPDATE_CURRENT, {
					latestProductState: payload
				});
			});
	},
};

const _fetchUserStats = (userId) => {
	return axios.get(getApiUrl(`users/${userId}/state/stats`));
};

export default {
	state,
	getters,
	mutations,
	actions
};
