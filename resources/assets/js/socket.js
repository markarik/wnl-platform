import * as io from 'socket.io-client'

export function getSocket() {
	if (!global.$socket) {
		global.$socket = io('46.101.174.6:9663')
	}
	return global.$socket
}
