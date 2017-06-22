import _ from 'lodash'
import * as types from '../mutations-types'
import {getApiUrl} from 'js/utils/env'
import {set} from 'vue'

const namespaced = true

function _getNotifications() {
	return axios.get(getApiUrl('users/current/notifications'))
}

const actions = {
	pullNotifications() {
		_getNotifications().then(response => {
			console.log(response.data)
		})
	},
	setupLiveNotifications(userId) {
		Echo.private(`user.${userId}`)
			.listen('.App.Notifications.Events.LiveNotificationCreated', (notification) => {
				console.log('Notification', notification);
			});
	},
}

export default {
	namespaced,
	actions,
}
