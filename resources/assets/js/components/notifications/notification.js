/**
 * A mixin with basic logic for a component of a Notification type.
 * @type {Object}
 */
import { isEmpty } from 'lodash'
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
		routeContext: {
			type: String|Object
		}
	},
	data() {
		return {
			loading: false,
		}
	},
	computed: {
		formattedTime () {
			return timeFromS(this.message.timestamp)
		},
		hasContext() {
			return !isEmpty(this.routeContext)
		},
		isRead() {
			return !!this.message.read_at
		},
	},
	methods: {
		...mapActions('notifications', ['markAsRead']),
		goToContext() {
			if (typeof this.routeContext === 'object') {
				this.$router.push(this.routeContext)
			} else if (typeof this.routeContext === 'string') {
				window.location.href=this.routeContext
			}
		},
	},
}
