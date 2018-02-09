const WnlSocket = {
    install(Vue, options) {
        const _getSocket = () => {
            if (!global.$socket) {
                global.$socket = io(`${envValue('chatHost')}:${envValue('chatPort')}`)
                global.$socket.on('error', _socketError);
            }
            return global.$socket
        }

        const connectSocket = () => {
            return new Promise((resolve, reject) => {
                let socket = _getSocket()
                socket.on('connected', () => {
                    Vue.$socket = socket
                })
                socket.on('connectionError', (data) => {
                    reject(data)
                })
            })
        }

        connectSocket()

        Vue.prototype.$socketEmit = (event, payload) => {
            Vue.socket.emit(event, payload)
        }
    }
}

export default WnlSocket
