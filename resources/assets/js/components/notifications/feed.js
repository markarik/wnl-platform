/**
 * A mixin with basic logic for a component of a Feed type.
 * @type {Object}
 */

import _ from 'lodash';
import { mapActions, mapGetters } from 'vuex';

export const feed = {
	computed: {
		...mapGetters('notifications', [
			'getOldestNotification',
			'getSortedNotifications',
			'hasMore',
			'isFetching',
		]),
		canShowMore() {
			return this.hasMore(this.channel);
		},
		fetching() {
			return this.isFetching(this.channel);
		},
		isEmpty() {
			return !this.fetching && this.totalNotifications === 0;
		},
		notifications() {
			return this.getSortedNotifications(this.channel);
		},
		notificationsWithComponentForEvent() {
			return this.notifications.filter(message => this.hasComponentForEvent(message));
		},
		showEndInfo() {
			return this.totalNotifications > this.limit && !this.canShowMore;
		},
		totalNotifications() {
			return _.size(this.notifications);
		},
	},
	methods: {
		...mapActions('notifications', [
			'pullNotifications',
		]),
		getEventComponent(message) {
			return `wnl-event-${message.event}`;
		},
		hasComponentForEvent(message) {
			const components = this.$options.components;

			return typeof components === 'object' &&
				Object.keys(components).indexOf(this.getEventComponent(message)) > -1;
		},
		loadMore(event) {
			event.stopPropagation();
			if (this.fetching) return;

			const extraParams = this.notificationsParams || {};

			this.pullNotifications([this.channel, {
				limit: this.limit,
				olderThan: this.getOldestNotification(this.channel).timestamp,
				...extraParams
			}]);
		},
	}
};
