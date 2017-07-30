/**
 * A mixin with basic logic for a component of a Notification type.
 * @type {Object}
 */
import { isEmpty, isObject } from 'lodash'
import { mapGetters, mapActions } from 'vuex'

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

			const name = this.routeContext.name

			if (name === 'screens') {
				const lessonId = this.routeContext.params.lessonId
				return this.$t('notifications.context.lesson', {
					lesson: this.getLesson(lessonId).name
				})
			} else if (name.indexOf('help') > -1) {
				return this.$t('notifications.context.page', {
					page: this.$t(`routes.help.${name}`)
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
