import Vue from 'vue'
import Raven from 'raven-js'
import RavenVue from 'raven-js/plugins/vue'
import { isDev, isDebug, envValue } from 'js/utils/env'

export default class Logger {
	// Log levels as per https://tools.ietf.org/html/rfc5424
	static get EMERGENCY() { return 'emergency'; }
	static get ALERT()     { return 'alert'; }
	static get CRITICAL()  { return 'critical'; }
	static get ERROR()     { return 'error'; }
	static get WARNING()   { return 'warning'; }
	static get NOTICE()    { return 'notice'; }
	static get INFO()      { return 'info'; }
	static get DEBUG()     { return 'debug'; }

	static get LEVELS() {
		return {
			'emergency' : 0,
			'alert'     : 1,
			'critical'  : 2,
			'error'     : 3,
			'warning'   : 4,
			'notice'    : 5,
			'info'      : 6,
			'debug'     : 7,
		}
	}

	constructor(options = {}) {
		if (this.useExternal()) {
			Raven
				.config(envValue('SENTRY_DSN_VUE_PUB'))
				.setExtraContext({
					logger: 'platform',
					env: envValue('appEnv')
				})
				.addPlugin(RavenVue, Vue)
				.install()
		}

		this.level     = envValue('APP_LOG_LEVEL')
		this.levelCode = Logger.LEVELS[this.level]
	}

	useExternal() {
		return !isDev()
	}

	log(level, message) {
		if (Logger.LEVELS[level] <= this.levelCode) {
			if (this.useExternal()) {
				Raven.captureMessage(message, { level })
			}

			if (isDebug()) {
				this.consolePrint(level, message)
			}
		}
	}

	consolePrint(level, message) {
		console.log(`wnlog-${level}: ${message}`)
	}

	emergency(message) {
		this.log(Logger.EMERGENCY, message)
	}

	alert(message) {
		this.log(Logger.ALERT, message)
	}

	critical(message) {
		this.log(Logger.CRITICAL, message)
	}

	error(message) {
		this.log(Logger.ERROR, message)
	}

	warning(message) {
		this.log(Logger.WARNING, message)
	}

	notice(message) {
		this.log(Logger.NOTICE, message)
	}

	info(message) {
		this.log(Logger.INFO, message)
	}

	debug(message) {
		this.log(Logger.DEBUG, message)
	}
}
