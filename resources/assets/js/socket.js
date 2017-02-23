import * as io from 'socket.io-client'

export function getSocket() {
	if (!global.$socket) {
		global.$socket = io('chat.platforma.wnl:9663')
	}
	return global.$socket
}
