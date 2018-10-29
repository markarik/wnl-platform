import * as io from 'socket.io-client'
import {envValue} from 'js/utils/env'
const EVENTS = {
	USER_EVENT: 'track_user_event',
	ROUTE_CHANGE_EVENT: 'track_route_change_event'
}

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
	install(Vue, {store, router}) {
		const onSocketError = (error) => {
			$wnl.logger.error(`Socket error: ${error}`)
		}

		const onSocketConnectionError = (err) => {
			socket.off('connect_error')
		}

		const socket = io(`${envValue('sadHost')}:${envValue('sadPort')}`)

		socket.on('error', onSocketError);
		socket.on('connected', () => {
			eventsQueue.start()
		})

		socket.on('connect_error', onSocketConnectionError)

		socket.on('reconnect', () => {
			socket.on('connect_error', onSocketConnectionError)
		})

		const eventsQueue = createEventsQueue()

		const getSharedEventContext = () => ({
			client_time: new Date().getTime() / 1000,
			user_id: store.getters.currentUserId
		})

		Vue.prototype.$trackUserEvent = (payload) => {
			const contextRoute = {};
			Object.keys(router.currentRoute.params).forEach(key => {
				const param = router.currentRoute.params[key];
				const column = _.snakeCase(key);
				const value = isNaN(param) ? param : Number(param);
				contextRoute[column] = value;
			});

			socket.emit(EVENTS.USER_EVENT, {
				...payload,
				...getSharedEventContext(),
				context_route: contextRoute,
			})
		}

		Vue.prototype.$trackUrlChange = (payload) => {
			socket.emit(EVENTS.ROUTE_CHANGE_EVENT, {
				...payload,
				...getSharedEventContext(),
				context: 'url_track',
				feature: 'url_tracking',
				action: 'open',
			});
		}
	}
}

export default EventsTracker
