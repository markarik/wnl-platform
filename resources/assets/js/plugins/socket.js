import * as io from 'socket.io-client'
import {envValue} from 'js/utils/env'

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

        return new Promise((resolve, reject) => {
            socket.on('connected', () => {
                resolve()
            })
            socket.on('connectionError', (data) => {
                reject(data)
            })

            Vue.prototype.$socketEmit = (event, payload) => {
                socket.emit(event, payload)
            }
            Vue.prototype.$socketJoinRoom = (roomId) => {
                socket.emit('join-room', {
                    room: roomId
                })
            }
            Vue.prototype.$socketSendMessage = (payload) => {
                socket.emit('send-message', payload)
            }
            Vue.prototype.$socketSetMessagesListener = (listener) => {
                socket.on('message-processed', listener)
            }
        })
    }
}

export default WnlSocket
