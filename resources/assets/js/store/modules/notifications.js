import _ from 'lodash'
import * as types from '../mutations-types'
import {getApiUrl, envValue as env} from 'js/utils/env'
import {set} from 'vue'

const namespaced = true

const state = {
	loading: true,
	notifications: {},
	channels: [],
	// user: 0
}

const getters = {
	isLoading: (state) => state.loading,
	notifications: (state) => state.notifications,
	user: (state) => state.user,
	unseen: (state) => {
		return _.pickBy(state.notifications, (notification) => {
			return !notification.seen_at
		})
	},
	unread: (state) => {
		return _.pickBy(state.notifications, (notification) => {
			return !notification.read_at
		})
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
	// [types.SET_NOTIFICATIONS_USER] (state, user) {
	// 	set(state, 'user', user)
	// }
}

const actions = {
	pullNotifications({commit}, channel) {
		_getNotifications(channel).then(response => {
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
	setupLiveNotifications({commit}, channel) {
		Echo.channel(channel.name)
			.listen('.App.Events.LiveNotificationCreated', (notification) => {
				console.log(notification)
				commit(types.ADD_NOTIFICATION, notification)
			});
	},
	markAsRead({commit, getters}, notification) {
		_updateNotification(getters.user, notification.id, {'read_at': 'now'})
			.then((response) => {
				commit(types.MODIFY_NOTIFICATION, {notification, value: response.data.read_at, field: 'read_at'})
			})
	},
	markAllAsSeen({commit, getters}, channel) {
		let data = _.mapValues(getters.unseen, (notification) => {
			return { 'seen_at' : 'now' }
		})

		_updateMany(channel, data)
			.then((response) => {
				_.each(response.data, (notification) => {
					commit(types.ADD_NOTIFICATION, notification)
				})
			})
	},
	markAllAsRead({commit, getters}, channel) {
		let data = _.mapValues(getters.unread, (notification) => {
			return { 'read_at' : 'now' }
		})

		_updateMany(channel, data)
			.then((response) => {
				_.each(response.data, (notification) => {
					commit(types.ADD_NOTIFICATION, notification)
				})
			})
	},
	initNotifications({dispatch}, channel) {
		dispatch('pullNotifications', channel)
		dispatch('setupLiveNotifications', channel)
	}
}


function _getNotifications(channel) {

	const conditions = {
		'query': {
			'where': [
				['read_at', '=', null],
				['channel', '=', channel.name]
			]
		}
	}
	return axios.post(getApiUrl(`users/${channel.userId}/notifications/.search`), conditions)
}

function _updateNotification(channel, notificationId, data) {
	return axios.patch(getApiUrl(`users/${channel.userId}/notifications/${notificationId}`), data)
}

function _updateMany(channel, data) {
	return axios.patch(getApiUrl(`users/${channel.userId}/notifications`), data)
}


export default {
	namespaced,
	state,
	mutations,
	getters,
	actions
}
