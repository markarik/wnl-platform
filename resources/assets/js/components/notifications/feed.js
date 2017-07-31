/**
 * A mixin with basic logic for a component of a Feed type.
 * @type {Object}
 */

import _ from 'lodash'
import { mapActions, mapGetters } from 'vuex'

export const feed = {
	computed: {
		...mapGetters('notifications', [
			'getOldestNotification',
			'getSortedNotifications',
			'hasMore',
			'isFetching',
		]),
		fetching() {
			return this.isFetching(this.channel)
		},
		totalNotifications() {
			return _.size(this.notifications)
		},
		isEmpty() {
			return !this.fetching && this.totalNotifications === 0
		},
		notifications() {
			return this.getSortedNotifications(this.channel)
		},
	},
	methods: {
		...mapActions('notifications', [
			'pullNotifications',
		]),
		getEventComponent(message) {
			return `wnl-event-${message.event}`
		},
		hasComponentForEvent(message) {
			const components = this.$options.components

			return typeof components === 'object' &&
				Object.keys(components).indexOf(this.getEventComponent(message)) > -1
		},
		loadMore() {
			if (this.fetching) return;

			this.pullNotifications([this.channel, {
				limit: this.limit,
				olderThan: this.getOldestNotification(this.channel).timestamp
			}])
		},
	}
}
