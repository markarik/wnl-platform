import { throttle } from 'lodash';

const THROTTLE_TIME = 1000 * 30;
const EVENTS = ['click', 'touchstart', 'keyup', 'mousemove', 'scroll'];
const trackingIntervalIds = {};
const inactivityTimerIds = {};
let activitiesConfig;

export const startActivityTracking = (userId, config) => {
	activitiesConfig = config;
	EVENTS.forEach((event) => {
		document.addEventListener(event, throttle(() => track(userId), THROTTLE_TIME), true);
	});
};

const track = (userId) => {
	Object.entries(activitiesConfig).forEach(([eventName, config]) => {
		if (!trackingIntervalIds[eventName]) {
			trackingIntervalIds[eventName] = window.setInterval(() => config.handle(userId), config.incrementBy);
		}

		clearTimeout(inactivityTimerIds[eventName]);
		inactivityTimerIds[eventName] = setTimeout(() => stopTracking(eventName), config.inactivityTime);
	});
};

const stopTracking = eventName => {
	window.clearInterval(trackingIntervalIds[eventName]);
	trackingIntervalIds[eventName] = null;
	inactivityTimerIds[eventName] = null;
};
