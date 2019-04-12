/**
 * A mixin with basic logic for a component of a Notification type.
 * @type {Object}
 */
import { decode } from 'he';
import { isEmpty, isObject, truncate, get } from 'lodash';
import { mapActions, mapGetters } from 'vuex';

import { timeFromS } from 'js/utils/time';

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
			objectTextLength: 75,
			subjectTextLength: 150,
		};
	},
	computed: {
		...mapGetters('course', ['getLesson']),
		contextInfo() {
			if (!isObject(this.routeContext)) return '';

			const route = this.routeContext.dynamic ? this.routeContext.route : this.routeContext;

			if (route.name === 'screens') {
				const lessonId = route.params.lessonId;
				const slide = route.params.slide;

				let contextInfo = this.$t('notifications.context.lesson', {
					lesson: truncate(this.getLesson(lessonId).name, {length: 30}),
				});

				if (get(this.message, 'objects.type') === 'slide' && slide) {
					contextInfo = `${this.$t('notifications.context.slide', {slide})} ${contextInfo}`;
				}

				return contextInfo;
			} else if (route.name === 'quizQuestion') {
				return this.$t('notifications.context.quizQuestion', {
					id: this.routeContext.params.id,
				});
			} else if (this.$te(`routes.help.${route.name}`)) {
				return this.$t('notifications.context.page', {
					page: this.$t(`routes.help.${route.name}`),
				});
			}

			return '';
		},
		deleted() {
			return !!this.message.deleted;
		},
		resolved() {
			return !!this.message.resolved;
		},
		formattedTime () {
			return timeFromS(this.message.timestamp);
		},
		hasContext() {
			return !isEmpty(this.routeContext);
		},
		hasFullContext() {
			return isObject(this.routeContext);
		},
		isRead() {
			return !!this.message.read_at;
		},
		isSeen() {
			return !!this.message.seen_at;
		},
		objectText() {
			if (!this.message.objects) return false;

			if (this.objectTextLength > 0) {
				return decode(truncate(this.message.objects.text, {length: this.objectTextLength}));
			}

			return decode(this.message.objects.text);
		},
		subjectText() {
			if (!this.message.subject) return false;

			if (this.objectTextLength > 0) {
				return decode(truncate(this.message.subject.text, {length: this.subjectTextLength}));
			}

			return decode(this.message.subject.text);
		},
		hasDynamicContext() {
			return !!get(this.message, 'context.dynamic');
		},
		dynamicRoute() {
			const {query, dynamic} = this.routeContext;

			return {
				name: 'dynamicContextMiddleRoute',
				params: {
					resource: dynamic.resource,
					context: dynamic.value
				},
				query
			};
		}
	},
	methods: {
		...mapActions('notifications', ['markAsRead', 'markAsSeen']),
		goToContext() {
			if (this.message.deleted) return;

			this.$emit('goingToContext');

			if (this.hasDynamicContext) {
				this.$router.push(this.dynamicRoute);
			} else if (typeof this.routeContext === 'object') {
				this.$router.push(this.routeContext);
			} else if (typeof this.routeContext === 'string') {
				window.location.href=this.routeContext;
			}
		},
	},
};
