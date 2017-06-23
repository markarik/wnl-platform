import _ from 'lodash'
import * as types from '../mutations-types'
import {getApiUrl} from 'js/utils/env'
import {set} from 'vue'

const namespaced = true

const state = {
	loading: true,
	notifications: {}
}

const getters = {}

const mutations = {
	[types.IS_LOADING] (state, isLoading) {
		set(state, 'loading', isLoading)
	},
	[types.SET_NOTIFICATIONS] (state, notifications) {
		set(state, 'notifications', notifications)
	},
	[types.ADD_NOTIFICATION] (state, notification) {
		set(state, 'notifications', {...state.notifications, [notification.id]: notification})
	},
	[types.MARK_NOTIFICATION_AS_READ] (state, notificationId, time) {
		// set(state, 'notifications', {...state.notifications, [notificationId]:{...notification, read_at: 2342343}})
	}
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
			// commit(types.SET_NOTIFICATIONS, )
			console.log(response.data)
			commit(types.IS_LOADING, false)
		})
	},
	setupLiveNotifications({rootGetters}) {
		Echo.private(`user.${rootGetters.currentUserId}`)
			.listen('.App.Notifications.Events.LiveNotificationCreated', (notification) => {
				console.log('Notification', notification);
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
