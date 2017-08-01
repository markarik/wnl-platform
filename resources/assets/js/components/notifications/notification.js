/**
 * A mixin with basic logic for a component of a Notification type.
 * @type {Object}
 */
import { isEmpty, isObject, truncate } from 'lodash'
import { mapActions, mapGetters } from 'vuex'

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
		...mapGetters('course', ['getLesson']),
		contextInfo() {
			if (!isObject(this.routeContext)) return ''

			const route = this.routeContext.name

			if (route === 'screens') {
				const lessonId = this.routeContext.params.lessonId
				const slide = this.routeContext.params.slide

				let contextInfo = this.$t('notifications.context.lesson', {
					lesson: _.truncate(this.getLesson(lessonId).name, {length: 30}),
				})

				if (_.get(this.message, 'objects.type') === 'slide' && slide) {
					contextInfo = `${this.$t('notifications.context.slide', {slide})} ${contextInfo}`
				}

				return contextInfo
			} else if (route === 'quizQuestion') {
				return this.$t('notifications.context.quizQuestion', {
					id: this.routeContext.params.id,
				})
			} else if (route.indexOf('help') > -1) {
				return this.$t('notifications.context.page', {
					page: this.$t(`routes.help.${route}`),
				})
			}

			return ''
		},
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
			this.$emit('goingToContext')
			if (typeof this.routeContext === 'object') {
				this.$router.push(this.routeContext)
			} else if (typeof this.routeContext === 'string') {
				window.location.href=this.routeContext
			}
		},
	},
}
