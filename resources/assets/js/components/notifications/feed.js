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
	computed: {
		...mapGetters('notifications', [
			'getOldestNotification',
			'getSortedNotifications',
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
			return new Promise((resolve) => {
				this.pullNotifications([this.channel, {
					limit: this.limit,
					olderThan: this.getOldestNotification(this.channel).timestamp
				}]).then(() => resolve())
			})
		},
	}
}
