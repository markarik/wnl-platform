import Vue from 'vue'

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
		}
	},
	methods: {
		removeAlert(timestamp) {
			Vue.delete(this.alerts, timestamp)
		},
		addAlert(message, cssClass = '', fading = false, timeout = 5000) {
			let timestamp = Date.now()

			this.alerts[timestamp] = {
				message: message,
				cssClass: cssClass,
			}

			if (fading) {
				this.timeouts[timestamp] = setTimeout(this.removeAlert, timeout, timestamp)
			}
		},
		alertInfo(message) {
			this.addAlert(message, 'is-info')
		},
		alertInfoFading(message, timeout = 5000) {
			this.addAlert(message, 'is-info', true, timeout)
		},
		alertError(message) {
			this.addAlert(message, 'is-error')
		},
		alertErrorFading(message, timeout = 5000) {
			this.addAlert(message, 'is-error', true, timeout)
		},
		alertSuccess(message) {
			this.addAlert(message, 'is-success')
		},
		alertSuccessFading(message, timeout = 5000) {
			this.addAlert(message, 'is-success', true, timeout)
		},
		onDelete(timestamp) {
			Vue.delete(this.timeouts, timestamp)
			this.removeAlert(timestamp)
		}
	}
}
