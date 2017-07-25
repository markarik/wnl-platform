/**
 * A mixin with basic logic for a component of a Feed type.
 * @type {Object}
 */

import _ from 'lodash'
import { mapActions, mapGetters } from 'vuex'

export const feed = {
	props: {
		limit: {
			default: 15,
			type: Number,
		}
	},
	data() {
		return {
			hasMore: true,
		}
	},
	computed: {
		...mapGetters('notifications', [
			'getOldestNotification',
			'getSortedNotifications',
			'isFetching',
			'isLoading',
		]),
		isEmpty() {
			return !this.isLoading && _.size(this.notifications) === 0
		},
		notifications() {
			return this.getSortedNotifications(this.channel)
		},
	},
	methods: {
		...mapActions('notifications', [
			'pullNotifications',
		]),
		loadMore() {
			if (this.isFetching) return;

			this.pullNotifications([this.channel, {
				limit: this.limit,
				olderThan: this.getOldestNotification(this.channel).timestamp
			}]).then((response) => {
				if (response.data.length < this.limit) {
					this.hasMore = false
				}
			})
		},
		getEventComponent(message) {
			return `wnl-event-${message.event}`
		},
		hasComponentForEvent(message) {
			const components = this.$options.components

			return typeof components === 'object' &&
				Object.keys(components).indexOf(this.getEventComponent(message)) > -1
		}
	}
}
