/**
 * A mixin with basic logic for a component of a Notification type.
 * @type {Object}
 */

import { mapActions } from 'vuex'

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
		isRead() {
			return this.message.read_at
		},
	},
	methods: {
		...mapActions('notifications', ['markAsRead']),
		goToContext() {
			if(!this.context) return false;

			if (typeof this.context === 'object') {
				this.$router.push(this.context)
			} else if (typeof this.context === 'string') {
				window.location.href=this.context
			}
		},
		setContext(context) {
			this.context = context
		},
	},
}
