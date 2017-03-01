import * as io from 'socket.io-client'

function getSocket() {
	if (!global.$socket) {
		global.$socket = io('chat.platforma.wnl:9663')
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
