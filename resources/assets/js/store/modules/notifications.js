import _ from 'lodash'
import * as types from '../mutations-types'
import {getApiUrl, envValue as env} from 'js/utils/env'
import {set, delete as destroy} from 'vue'

const namespaced = true

const state = {
	fetching: {},
	hasMore: {},
	notifications: {},
}

const getters = {
	hasMore: (state) => (channel) => state.hasMore[channel],
	isFetching: (state) => (channel) => {
		return _.isUndefined(state.fetching[channel]) || !!state.fetching[channel]
	},
	notifications: (state) => state.notifications,
	moderatorsChannel: (state, getters, rootState, rootGetters) => {
		return rootGetters.isModerator && `private-role.moderator.${rootGetters.currentUserId}`
	},
	streamChannel: (state, getters, rootState, rootGetters) => {
		return rootGetters.currentUserId && `private-stream.${rootGetters.currentUserId}`
	},
	userChannel: (state, getters, rootState, rootGetters) => {
		return rootGetters.currentUserId && `private-${rootGetters.currentUserId}`
	},
	filterSlides: (state, getters) => (channel) => {
		return _.pickBy(getters.getSortedNotifications(channel), (notification) => {
			return notification.objects && notification.objects.type === 'slide'
		})
	},
	filterQuiz: (state, getters) => (channel) => {
		return _.pickBy(getters.getSortedNotifications(channel), (notification) => {
			return notification.objects && notification.objects.type === 'quiz_question'
		})
	},
	filterQna: (state, getters) => (channel) => {
		return _.pickBy(getters.getSortedNotifications(channel), (notification) => {
			return notification.objects &&
				(notification.objects.type === 'qna_question' || notification.objects.type === 'qna_answer') ||
				notification.subject && notification.subject.type === 'qna_question'
		})
	},
	getChannelNotifications: (state) => (channel) => {
		return _.pickBy(state.notifications, (notification) => notification.channel === channel)
	},
	getUnseen: (state, getters) => (channel) => {
		return _.pickBy(getters.getChannelNotifications(channel), (notification) => !notification.seen_at)
	},
	getUnread: (state, getters) => (channel) => {
		return _.pickBy(getters.getChannelNotifications(channel), (notification) => !notification.read_at)
	},
	getRead: (state, getters) => (channel) => {
		return _.pickBy(getters.getChannelNotifications(channel), (notification) => notification.read_at)
	},
	getSortedNotifications: (state, getters) => (channel) => {
		const notifications = getters.getChannelNotifications(channel)
		return _.reverse(_.sortBy(_.values(notifications), (notification) => notification.timestamp))
	},
	getOldestNotification: (state, getters) => (channel) => {
		return _.last(getters.getSortedNotifications(channel)) || {}
	},
}

const mutations = {
	[types.ADD_NOTIFICATION] (state, notification) {
		set(state.notifications, notification.id, notification)
	},
	[types.CHANNEL_HAS_MORE] (state, {channel, hasMore}) {
		set(state.hasMore, channel, hasMore)
	},
	[types.IS_FETCHING] (state, {channel, isFetching}) {
		set(state.fetching, channel, isFetching)
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
	pullNotifications({commit, rootGetters}, [ channel, options ]) {
		commit(types.IS_FETCHING, {channel, isFetching: true})
		return new Promise ((resolve, reject) => {
			_getNotifications(channel, rootGetters.currentUserId, options)
				.then(response => {
					if (typeof response.data[0] !== 'object') {
						commit(types.CHANNEL_HAS_MORE, {channel, hasMore: false})
						commit(types.IS_FETCHING, {channel, isFetching: false})
						resolve(response)
					}

					_.each(response.data, (notification) => {
						commit(types.ADD_NOTIFICATION, notification)
					})

					commit(types.CHANNEL_HAS_MORE, {
						channel,
						hasMore: !!options.limit && response.data.length >= options.limit
					})
					commit(types.IS_FETCHING, {channel, isFetching: false})

					resolve(response)
				})
		})
	},
	setupLiveNotifications({commit}, channel) {
		Echo.channel(channel)
			.listen('.App.Events.Live.LiveNotificationCreated', (notification) => {
				commit(types.ADD_NOTIFICATION, {...notification, channel})
			});
	},
	markAsRead({commit, getters, rootGetters}, {notification, channel}) {
		return _updateNotification(rootGetters.currentUserId, notification.id, {'read_at': 'now'})
			.then((response) => {
				commit(types.MODIFY_NOTIFICATION, {notification, value: response.data.read_at, field: 'read_at'})
			})
			.catch((error) => {
				commit(types.MODIFY_NOTIFICATION, {notification, value: true, field: 'deleted'})
			})
	},
	markAsSeen({commit, getters, rootGetters}, {notification, channel}) {
		return _updateNotification(rootGetters.currentUserId, notification.id, {'seen_at': 'now'})
			.then((response) => {
				commit(types.MODIFY_NOTIFICATION, {notification, value: response.data.seen_at, field: 'seen_at'})
			})
			.catch((error) => {
				commit(types.MODIFY_NOTIFICATION, {notification, value: true, field: 'deleted'})
			})

	},
	markAsUnread({commit, getters, rootGetters}, {notification, channel}) {
		return _updateNotification(rootGetters.currentUserId, notification.id, {'read_at': null})
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

		return _updateMany(rootGetters.currentUserId, data)
			.then((response) => {
				_.each(response.data, (notification) => {
					commit(types.ADD_NOTIFICATION, notification)
				})
				return response
			})
	},
	initNotifications({getters, dispatch}) {
		dispatch('pullNotifications', [getters.userChannel, {limit: 15}])
		dispatch('setupLiveNotifications', getters.userChannel)

		dispatch('pullNotifications', [getters.streamChannel, {limit: 100, unread: true}])
		dispatch('setupLiveNotifications', getters.streamChannel)
	}
}

function _getNotifications(channel, userId, options) {
	const conditions = {
		channel
	}

	if (options.hasOwnProperty('unread')) {
		conditions.unread = options.unread
	}

	if (options.hasOwnProperty('limit')) {
		conditions.limit = options.limit
	}

	if (options.hasOwnProperty('olderThan') && options.olderThan) {
		options.older_than = options.olderThan
	}

	return axios.post(getApiUrl(`users/${userId}/notifications/query`), conditions)
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
