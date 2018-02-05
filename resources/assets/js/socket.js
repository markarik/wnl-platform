import * as io from 'socket.io-client'
import {envValue} from 'js/utils/env'

function getSocket() {
	if (!global.$socket) {
		global.$socket = io(`${envValue('chatHost')}:${envValue('chatPort')}`)
		global.$socket.on('error', _socketError);
	}
	return global.$socket
}

function destroySocket() {
	if (!!global.$socket) {
		delete global.$socket
	}
	return true
}

export function connect() {
	return new Promise((resolve, reject) => {
		let socket = getSocket()
		socket.on('connected', (data) => {
			resolve(socket)
		})
		socket.on('connectionError', (data) => {
			reject(data)
		})
	})
}

export function disconnect() {
	return new Promise((resolve, reject) => {
		let socket = getSocket()
		// TODO: Mar 1, 2017 - Can we verify if the connection is closed?
		socket.emit('disconnect')
		destroySocket()
		resolve()
	})
}

function _socketError(error) {
	if (error === 'Authentication error') {
		window.location.replace('/login');
		return
	}
	$wnl.logger.error(`Socket error: ${error}`)
}
