import * as io from 'socket.io-client'

export function getSocket() {
	if (!global.$socket) {
		global.$socket = io($wnl.chatHost + ':' + $wnl.chatPort)
	}
	return global.$socket
}
