import * as io from 'socket.io-client';
import { snakeCase } from 'lodash';
import { envValue } from 'js/utils/env';
import { gaEvent, gaPageView } from 'js/utils/tracking';
const EVENTS = {
	USER_EVENT: 'track_user_event',
	USER_ACTIVITY_EVENT: 'track_user_activity_event',
	ROUTE_CHANGE_EVENT: 'track_route_change_event'
};

const createEventsQueue = () => {
	const events = [];
	let started = false;

	return {
		push: (event) => {
			if (started) event();
			else events.push(event);
		},
		start: () => {
			started = true;

			while (events.length >= 1) {
				events.shift()();
			}
		}
	};
};

const EventsTracker = {
	install(Vue, { store, router }) {
		const onSocketError = (error) => {
			// Happens e.g. when server is restared or session expires
			// Socket.io handles it by reconnecting or creating new session
			// No need to pollute the logs
			if (
				error.type === 'TransportError' &&
				(error.message === 'xhr poll error' || error.message === 'xhr post error')
			) {
				return;
			}

			$wnl.logger.error(`Sad socket error: ${error}`);
		};

		const onSocketConnectionError = () => {
			socket.off('connect_error');
		};

		const socket = io(`${envValue('sadHost')}:${envValue('sadPort')}`);

		socket.on('error', onSocketError);
		socket.on('connected', () => {
			eventsQueue.start();
		});

		socket.on('connect_error', onSocketConnectionError);

		socket.on('reconnect', () => {
			socket.on('connect_error', onSocketConnectionError);
		});

		const eventsQueue = createEventsQueue();

		const getSharedEventContext = async () => {
			await store.dispatch('setupCurrentUser');
			return {
				client_time: new Date().getTime() / 1000,
				user_id: store.getters.currentUserId,
			};
		};

		Vue.prototype.$trackUserEvent = async payload => {
			const contextRoute = {};
			Object.keys(router.currentRoute.params).forEach(key => {
				const param = router.currentRoute.params[key];
				const column = snakeCase(key);
				const value = isNaN(param) ? param : Number(param);
				contextRoute[column] = value;
			});

			const gaCategory = [
				...(payload.context ? [payload.context] : []),
				...(payload.subcontext ? [payload.subcontext] : []),
				...(payload.feature ? [payload.feature]: []),
				...(payload.feature_component ? [payload.feature_component] : []),
			].join('-');

			gaEvent(gaCategory, payload.action, payload.target);
			eventsQueue.push(async () => {
				socket.emit(EVENTS.USER_EVENT, {
					...payload,
					...(await getSharedEventContext()),
					context_route: contextRoute,
				});
			});
		};

		Vue.prototype.$trackUrlChange = (payload) => {
			gaPageView();
			eventsQueue.push(async () => {
				socket.emit(EVENTS.ROUTE_CHANGE_EVENT, {
					...payload,
					...(await getSharedEventContext()),
				});
			});
		};

		Vue.prototype.$trackUserActivity = async () => {
			eventsQueue.push(async () => {
				socket.emit(EVENTS.USER_ACTIVITY_EVENT, {
					...await getSharedEventContext(),
					status: true
				});
			});
		};
	}
};

export default EventsTracker;
