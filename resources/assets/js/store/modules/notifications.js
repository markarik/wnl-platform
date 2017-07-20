import _ from 'lodash'
import * as types from '../mutations-types'
import {getApiUrl, envValue as env} from 'js/utils/env'
import {set} from 'vue'

const namespaced = true

const state = {
	loading: true,
	notifications: {},
}

const getters = {
	isLoading: (state) => state.loading,
	notifications: (state) => state.notifications,
	userChannel: (state, getters, rootState, rootGetters) => {
		return rootGetters.currentUserId && `private-${rootGetters.currentUserId}`
	},
	moderatorsChannel: (state, getters, rootState, rootGetters) => {
		return rootGetters.isAdmin && `private-role.moderator.${rootGetters.currentUserId}`
	},
	getUnseen: (state, getters) => (channel) => {
		return _.pickBy(getters.getChannelNotifications(channel), (notification) => !notification.seen_at)
	},
	getUnread: (state, getters) => (channel) => {
		return _.pickBy(getters.getChannelNotifications(channel), (notification) => !notification.read_at)
	},
	getChannelNotifications: ({notifications}, getters) => (channel) => {
		return _.pickBy(getters.getSortedNotifications, (notification) => notification.channel === channel)
	},
	getSortedNotifications: ({notifications}) => {
		return _.reverse(_.sortBy(_.values(notifications), (notification) => notification.timestamp))
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
}

const actions = {
	pullNotifications({commit, rootGetters}, channel) {
		_getNotifications(channel, rootGetters.currentUserId).then(response => {
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
		Echo.channel(channel)
			.listen('.App.Events.LiveNotificationCreated', (notification) => {
				console.log(notification)
				commit(types.ADD_NOTIFICATION, {...notification, channel})
			});
	},
	markAsRead({commit, getters, rootGetters}, {notification, channel}) {
		_updateNotification(rootGetters.currentUserId, notification.id, {'read_at': 'now'})
			.then((response) => {
				commit(types.MODIFY_NOTIFICATION, {notification, value: response.data.read_at, field: 'read_at'})
			})
	},
	markAllAsSeen({commit, getters, rootGetters}, channel) {
		let data = _.mapValues(getters.getUnseen(channel), (notification) => {
			return {'seen_at': 'now'}
		})

		_updateMany(rootGetters.currentUserId, data)
			.then((response) => {
				_.each(response.data, (notification) => {
					commit(types.ADD_NOTIFICATION, notification)
				})
			})
	},
	markAllAsRead({commit, getters, rootGetters}, channel) {
		let data = _.mapValues(getters.getUnread(channel), (notification) => {
			return {'read_at': 'now'}
		})

		_updateMany(rootGetters.currentUserId, data)
			.then((response) => {
				_.each(response.data, (notification) => {
					commit(types.ADD_NOTIFICATION, notification)
				})
			})
	},
	initNotifications({getters, dispatch}) {
		dispatch('pullNotifications', getters.userChannel)
		dispatch('setupLiveNotifications', getters.userChannel)

		if (getters.moderatorsChannel) {
			dispatch('pullNotifications', getters.moderatorsChannel)
			dispatch('setupLiveNotifications', getters.moderatorsChannel)
		}
	}
}


function _getNotifications(channel, userId) {

	const conditions = {
		'query': {
			'where': [
				['read_at', '=', null],
				['channel', '=', channel]
			]
		}
	}
	return axios.post(getApiUrl(`users/${userId}/notifications/.search`), conditions)
}

function _updateNotification(userId, notificationId, data) {
	return axios.patch(getApiUrl(`users/${userId}/notifications/${notificationId}`), data)
}

function _updateMany(userId, data) {
	return axios.patch(getApiUrl(`users/${userId}/notifications`), data)
}


export default {
	namespaced,
	state,
	mutations,
	getters,
	actions
}
