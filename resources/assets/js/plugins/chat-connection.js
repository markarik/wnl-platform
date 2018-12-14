import * as io from 'socket.io-client';
import {envValue} from 'js/utils/env';
import { SOCKET_CONNECTION_ERROR, SOCKET_CONNECTION_RECONNECTED } from '../store/mutations-types';

export const SOCKET_EVENT_SEND_MESSAGE = 'sendMessage';
export const SOCKET_EVENT_MESSAGE_PROCESSED = 'messageProcessed';
export const SOCKET_EVENT_USER_SENT_MESSAGE = 'userSentMessage';
export const SOCKET_EVENT_JOIN_ROOM = 'joinRoom';
export const SOCKET_EVENT_JOIN_ROOM_SUCCESS = 'joinRoomSuccess';
export const SOCKET_EVENT_LEAVE_ROOM = 'leaveRoom';
export const SOCKET_EVENT_MARK_ROOM_AS_READ = 'markRoomAsRead';
export const SOCKET_EVENT_MARK_ROOM_AS_READ_SUCCESS = 'markRoomAsReadSuccess';

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

const WnlSocket = {
	install(Vue, {store}) {
		const onSocketError = (error) => {
			if (error === 'Unauthorized') {
				window.location.replace('/login');
				return;
			}
			$wnl.logger.error(`Socket error: ${error}`);
		};

		if (!global.$socket) {
			global.$socket = io(`${envValue('chatHost')}:${envValue('chatPort')}`);
			global.$socket.on('error', onSocketError);
		}

		const eventsQueue = createEventsQueue();
		const socket = global.$socket;
		socket.on('connected', () => {
			eventsQueue.start();
		});

		const onSocketConnectionError = (err) => {
			store.dispatch(SOCKET_CONNECTION_ERROR);
			socket.off('connect_error');
		};

		socket.on('connect_error', onSocketConnectionError);

		socket.on('reconnect', () => {
			store.dispatch(SOCKET_CONNECTION_RECONNECTED);
			socket.on('connect_error', onSocketConnectionError);
		});

		Vue.prototype.$socketEmit = (event, payload) => {
			eventsQueue.push(() => socket.emit(event, payload));
		};

		Vue.prototype.$socketRegisterListener = (event, listener) => {
			socket.on(event, listener);
		};

		Vue.prototype.$socketRemoveListener = (event, listener) => {
			socket.off(event, listener);
		};

		Vue.prototype.$socketJoinRoom = (room, pointer = null) => {
			return new Promise((resolve, reject) => {
				eventsQueue.push(() => {
					socket.emit(SOCKET_EVENT_JOIN_ROOM, {room, pointer});

					const timerId = setTimeout(() => {
						$wnl.logger.error('Failed to connect to room', room);
						reject();
					}, 5000);

					socket.on(SOCKET_EVENT_JOIN_ROOM_SUCCESS, (data) => {
						if (room === data.room) {
							clearTimeout(timerId);
							resolve(data);
						}
					});
				});
			});
		};

		Vue.prototype.$socketSendMessage = (payload) => {
			return new Promise((resolve, reject) => {
				eventsQueue.push(() => {
					socket.emit(SOCKET_EVENT_SEND_MESSAGE, payload);

					const timerId = setTimeout(() => {
						$wnl.logger.error('Unable to send message', payload.message);
						return reject();
					}, 5000);

					socket.on(SOCKET_EVENT_MESSAGE_PROCESSED, (data) => {
						if (payload.room.id === data.room.id) {
							clearTimeout(timerId);
							return resolve(data);
						}
					});
				});
			});
		};

		Vue.prototype.$socketMarkRoomAsRead = (room) => {
			return new Promise((resolve, reject) => {
				eventsQueue.push(() => {
					socket.emit(SOCKET_EVENT_MARK_ROOM_AS_READ, room);

					const timerId = setTimeout(() => {
						$wnl.logger.error('Unable to mark room as read', room.id);
						reject();
					}, 5000);

					socket.on(SOCKET_EVENT_MARK_ROOM_AS_READ_SUCCESS, (data) => {
						if (room.id === data.room.id) {
							clearTimeout(timerId);
							resolve(data);
						}
					});
				});
			});
		};
	}
};

export default WnlSocket;
