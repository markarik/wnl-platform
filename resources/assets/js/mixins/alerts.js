import Vue from 'vue';

export var alerts = {
	data() {
		return {
			/**
			 * Contains all alerts a given component displays.
			 * Uses a timestamp of an alert as a key and two properties:
			 * message and class to control the alert.
			 * @type {Object}
			 */
			alerts: {},
			timeouts: {},
		};
	},
	methods: {
		removeAlert(timestamp) {
			Vue.delete(this.alerts, timestamp);
		},
		addAlert(message, cssClass = '', fading = false, timeout = 3000) {
			let timestamp = Date.now();

			Vue.set(this.alerts, timestamp, { message, cssClass });

			if (fading) {
				this.timeouts[timestamp] = setTimeout(this.removeAlert, timeout, timestamp);
			}
		},
		info(message) {
			this.addAlert(message, 'is-info');
		},
		infoFading(message, timeout = 3000) {
			this.addAlert(message, 'is-info', true, timeout);
		},
		error(message) {
			this.addAlert(message, 'is-danger');
		},
		errorFading(message, timeout = 3000) {
			this.addAlert(message, 'is-danger', true, timeout);
		},
		success(message) {
			this.addAlert(message, 'is-success');
		},
		successFading(message, timeout = 3000) {
			this.addAlert(message, 'is-success', true, timeout);
		},
		onDelete(timestamp) {
			Vue.delete(this.timeouts, timestamp);
			this.removeAlert(timestamp);
		}
	}
};
