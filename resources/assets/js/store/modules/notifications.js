import _ from 'lodash'
import * as types from '../mutations-types'
import {getApiUrl, envValue as env} from 'js/utils/env'
import {set} from 'vue'

const namespaced = true

const state = {
	loading: true,
	notifications: {},
	channels: [],
}

const getters = {
	isLoading: (state) => state.loading,
	notifications: (state) => state.notifications,
	userChannel: (state, getters, rootState, rootGetters) => rootGetters.currentUserId && `private-${rootGetters.currentUserId}`,
	moderatorsChannel: (state, getters, rootState, rootGetters) => rootGetters.isAdmin && `role-role.moderator`,
	unseen: (state) => {
		return _.pickBy(state.notifications, (notification) => !notification.seen_at)
	},
	unread: (state) => {
		return _.pickBy(state.notifications, (notification) => !notification.read_at)
	},
	getChannelNotifications: ({notifications}) => (channel) => _.pickBy(notifications, (notification) => notification.channel === channel)
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
				commit(types.ADD_NOTIFICATION, notification)
			});
	},
	markAsRead({commit, getters, rootGetters}, notification) {
		_updateNotification(rootGetters.currentUserId, notification.id, {'read_at': 'now'})
			.then((response) => {
				commit(types.MODIFY_NOTIFICATION, {notification, value: response.data.read_at, field: 'read_at'})
			})
	},
	markAllAsSeen({commit, getters, rootGetters}) {
		let data = _.mapValues(getters.unseen, (notification) => {
			return { 'seen_at' : 'now' }
		})

		_updateMany(rootGetters.currentUserId, data)
			.then((response) => {
				_.each(response.data, (notification) => {
					commit(types.ADD_NOTIFICATION, notification)
				})
			})
	},
	markAllAsRead({commit, getters, rootGetters}) {
		let data = _.mapValues(getters.unread, (notification) => {
			return { 'read_at' : 'now' }
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
