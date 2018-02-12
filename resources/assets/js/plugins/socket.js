import * as io from 'socket.io-client'
import {envValue} from 'js/utils/env'

export const SOCKET_EVENT_SEND_MESSAGE = 'send-message'
export const SOCKET_EVENT_MESSAGE_PROCESSED = 'message-processed'
export const SOCKET_EVENT_JOIN_ROOM = 'join-room'

const SOCKET_EVENTS = [SOCKET_EVENT_SEND_MESSAGE, SOCKET_EVENT_MESSAGE_PROCESSED]

const WnlSocket = {
    install(Vue, options) {
        const onSocketError = (error) => {
            if (error === 'Authentication error') {
                window.location.replace('/login');
                return
            }
            $wnl.logger.error(`Socket error: ${error}`)
        }

        const getSocketInstance = () => {
            if (!global.$socket) {
                global.$socket = io(`${envValue('chatHost')}:${envValue('chatPort')}`)
                global.$socket.on('error', onSocketError);
            }
            return global.$socket
        }

        const socket = getSocketInstance()

        socket.on('connected', () => console.log('socket connected...'))
        socket.on('connectionError', () => console.error('socket connection failed'))

        Vue.prototype.$socketEmit = (event, payload) => {
            socket.emit(event, payload)
        }

        Vue.prototype.$socketRegisterListener = (event, listener) => {
            socket.on(event, listener)
        }

        Vue.prototype.$socketRemoveListener = (event, listener) => {
            socket.off(event, listener)
        }
    }
}

export default WnlSocket
