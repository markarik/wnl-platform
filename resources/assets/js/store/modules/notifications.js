import _ from 'lodash'
import * as types from '../mutations-types'
import {getApiUrl} from 'js/utils/env'
import {set} from 'vue'

const namespaced = true

const state = {
	loading: true,
	notifications: {}
}

const getters = {
	isLoading: (state) => state.loading,
	notifications: (state) => state.notifications,
}

const mutations = {
	[types.IS_LOADING] (state, isLoading) {
		set(state, 'loading', isLoading)
	},
	// [types.SET_NOTIFICATIONS] (state, notifications) {
	// 	set(state, 'notifications', notifications)
	// },
	[types.ADD_NOTIFICATION] (state, notification) {
		set(state, 'notifications', {[notification.id]: notification, ...state.notifications})
	},
	// [types.MARK_NOTIFICATION_AS_READ] (state, notification) {
	// 	set(state, 'notifications', {...state.notifications, [notification.id]:{...notification, read_at: new Date().getTime()}})
	// }
	// [types.RESET_MODULE] (state) {
	// 	let initialState = getInitialState()
	// 	Object.keys(initialState).forEach((field) => {
	// 		set(state, field, initialState[field])
	// 	})
	// },
}

const actions = {
	pullNotifications({commit}) {
		_getNotifications().then(response => {
			if (typeof response.data[0] !== 'object') {
				commit(types.IS_LOADING, false)
				return false
			}
			_.each(response.data, (notification) => {
				commit(types.ADD_NOTIFICATION, notification)
			})

			commit(types.IS_LOADING, false)
		})
	},
	setupLiveNotifications({rootGetters, commit}) {
		Echo.private(`user.${rootGetters.currentUserId}`)
			.listen('.App.Notifications.Events.LiveNotificationCreated', (notification) => {
				commit(types.ADD_NOTIFICATION, notification)
			});
	},
}

function _getNotifications() {
	return axios.get(getApiUrl('users/current/notifications'))
}

export default {
	namespaced,
	state,
	mutations,
	getters,
	actions
}
