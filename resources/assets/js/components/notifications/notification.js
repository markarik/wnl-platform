/**
 * A mixin with basic logic for a component of a Notification type.
 * @type {Object}
 */

import { mapActions } from 'vuex'

import { timeFromS } from 'js/utils/time'

export const notification = {
	props: {
		channel: {
			required: true,
			type: String,
		},
		message: {
			required: true,
			type: Object,
		},
	},
	data() {
		return {
			context: {},
		}
	},
	computed: {
		componentName() {
			return `wnl-event-${this.message.event}`
		},
		formattedTime () {
			return timeFromS(this.message.timestamp)
		},
		hasComponent() {
			const components = this.$options.components
			return typeof components === 'object' &&
				Object.keys(components).indexOf(this.componentName) > -1
		},
		hasContext() {
			return this.context.length > 0
		},
		isRead() {
			return !!this.message.read_at
		},
	},
	methods: {
		...mapActions('notifications', ['markAsRead']),
		goToContext() {
			if(!this.hasContext) return false;

			this.markAsRead({notification: this.message, channel: this.channel})
				.then(() => {
					if (typeof this.context === 'object') {
						this.$router.push(this.context)
					} else if (typeof this.context === 'string') {
						window.location.href=this.context
					}
				})
		},
		setContext(context) {
			this.context = context
		},
	},
}
