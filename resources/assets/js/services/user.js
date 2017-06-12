import axios from 'axios'
import {getApiUrl} from 'js/utils/env'

let currentUser, settings

export function getCurrentUser() {
	if (currentUser) {
		return Promise.resolve(currentUser)
	}

	const promisedUser = axios.get(getApiUrl('users/current/profile'))

	return promisedUser.then((result) => {
		currentUser = result

		return result
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

export function getDefaultSettings() {
	return $wnl.defaultSettings
}
