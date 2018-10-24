import * as io from 'socket.io-client'
import {envValue} from 'js/utils/env'
import { SOCKET_CONNECTION_ERROR, SOCKET_CONNECTION_RECONNECTED } from '../store/mutations-types';

const createEventsQueue = () => {
	const events = []
	let started = false

	return {
		push: (event) => {
			if (started) event()
			else events.push(event)
		},
		start: () => {
			started = true

			while (events.length >= 1) {
				events.shift()()
			}
		}
	}
}

const EventsTracker = {
	install(Vue, {store}) {
		const onSocketError = (error) => {
			$wnl.logger.error(`Socket error: ${error}`)
		}

		const onSocketConnectionError = (err) => {
			store.dispatch(SOCKET_CONNECTION_ERROR)
			socket.off('connect_error')
		}

		const socket = io(`${envValue('sadHost')}:${envValue('sadPort')}`)

		socket.on('error', onSocketError);
		socket.on('connected', () => {
			eventsQueue.start()
		})

		socket.on('connect_error', onSocketConnectionError)

		socket.on('reconnect', () => {
			store.dispatch(SOCKET_CONNECTION_RECONNECTED)
			socket.on('connect_error', onSocketConnectionError)
		})

		const eventsQueue = createEventsQueue()

		Vue.prototype.$trackEvent = (event, payload) => {
			socket.emit('track', {...payload, time: new Date().getTime()})
		}
	}
}

export default EventsTracker
