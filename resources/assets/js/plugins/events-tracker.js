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

		const eventsQueue = createEventsQueue();

		const getSharedEventContext = async () => {
			await store.dispatch('setupCurrentUser');
			return {
				client_time: new Date().getTime() / 1000,
				user_id: store.getters.currentUserId,
			}
		};

		Vue.prototype.$trackUserEvent = async payload => {
			const contextRoute = {};
			const available_columns = [
				'category_name',
				'root_category_name',
				'lesson_id',
				'screen_id',
				'course_id',
				'slide',
				'quiz_question_id'
			]
			Object.keys(router.currentRoute.params).forEach(key => {
				const param = router.currentRoute.params[key];
				const column = _.snakeCase(key);
				if (!available_columns.includes(column)) return;

				contextRoute[column] = isNaN(param) ? param : Number(param);
			});

			socket.emit(EVENTS.USER_EVENT, {
				...payload,
				...(await getSharedEventContext()),
				context_route: contextRoute,
			})
		};

		Vue.prototype.$trackUrlChange = async payload => {
			socket.emit(EVENTS.ROUTE_CHANGE_EVENT, {
				...payload,
				...(await getSharedEventContext()),
			});
		}
	}
}

export default EventsTracker
