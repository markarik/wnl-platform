import _ from 'lodash'
import * as types from '../mutations-types'
import {getApiUrl, envValue as env} from 'js/utils/env'
import {set} from 'vue'

const namespaced = true

const state = {
	loading: true,
	notifications: {},
	user: 0
}

const getters = {
	isLoading: (state) => state.loading,
	notifications: (state) => state.notifications,
	user: (state) => state.user,
	unseenCount: (state) => {
		return _.filter(state.notifications, (notification) => {
			return !notification.seen_at
		}).length
	},
}

const mutations = {
	[types.IS_LOADING] (state, isLoading) {
		set(state, 'loading', isLoading)
	},
	[types.ADD_NOTIFICATION] (state, notification) {
		set(state.notifications, notification.id, notification)
	},
	[types.MODIFY_NOTIFICATION] (state, payload) {
		set(state, 'notifications', {
			...state.notifications,
			[payload.notification.id]: {
				...payload.notification,
				[payload.field]: payload.value
			}
		})
	},
	[types.SET_NOTIFICATIONS_USER] (state, user) {
		set(state, 'user', user)
	}
}

const actions = {
	pullNotifications({commit}, userId) {
		_getNotifications(userId).then(response => {
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
	setupLiveNotifications({commit}, userId) {
		Echo.private(`user.${userId}`)
			.listen('.App.Notifications.Events.LiveNotificationCreated', (notification) => {
				commit(types.ADD_NOTIFICATION, notification)
			});
	},
	markAsRead({commit, getters}, notification) {
		_updateNotification(getters.user, notification.id, {'read_at': 'now'})
			.then((response) => {
				commit(types.MODIFY_NOTIFICATION, {notification, value: response.data.read_at, field: 'read_at'})
			})
	},
	markAsSeen({commit, getters}, notification) {
		// _updateNotification(getters.user, notification.id)
		// 	.then((response) => {
		// 		commit(types.MARK_NOTIFICATION_AS_READ, {notification, time: response.data.seen_at, field:'read_at'})
		// 	})
	},
	initNotifications({commit, dispatch, rootGetters}) {
		let userId = rootGetters.currentUserId
		if (rootGetters.hasRole('moderator')) {
			userId = env('MODERATORS_CHANNEL')
		}

		commit(types.SET_NOTIFICATIONS_USER, userId)
		dispatch('pullNotifications', userId)
		dispatch('setupLiveNotifications', userId)
	}
}


function _getNotifications(userId) {
	const conditions = {
		'query': {
			'where': [
				['read_at', '=', null]
			]
		}
	}
	return axios.post(getApiUrl(`users/${userId}/notifications/.search`), conditions)
}

function _updateNotification(userId, notificationId, data) {
	return axios.patch(getApiUrl(`users/${userId}/notifications/${notificationId}`), data)
}


export default {
	namespaced,
	state,
	mutations,
	getters,
	actions
}
