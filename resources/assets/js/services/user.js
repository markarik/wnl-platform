import axios from 'axios'
import {getApiUrl} from 'js/utils/env'

let currentUser, settings

export function getCurrentUser() {
	if (currentUser) {
		return Promise.resolve(currentUser)
	}

	return Promise.all([
		axios.get(getApiUrl('users/current/profile?include=roles')),
		axios.get(getApiUrl('user_subscription/current'))
	]).then(([userResponse, subscriptionResponse]) => {

		const {included, ...profile} = userResponse.data;
		profile.roles = Object.values(included.roles)
			.map(role => role.name)

		currentUser = {
			...profile,
			subscription: subscriptionResponse.data
		}
		return currentUser;
	})
}

export function getUserSettings() {
	if (settings) {
		return Promise.resolve(settings)
	}

	const promisedSettings = axios.get(getApiUrl('users/current/settings'))

	return promisedSettings.then((result) => {
		settings = result

		return result
	})
}

export function setUserSettings(settings) {
	return axios.put(getApiUrl('users/current/settings'), settings)
}

export function getDefaultSettings() {
	return $wnl.defaultSettings
}
