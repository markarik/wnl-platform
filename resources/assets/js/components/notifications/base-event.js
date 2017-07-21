/**
 * A mixin with basic logic for a component of an Event type.
 * @type {Object}
 */

export const baseEvent = {
	computed: {
		contextRoute() {
			try {
				return this.buildContext()
			} catch (e) {
				// It means there were not enough information to build full context
				// and a referer will be returned instead.
			}

			return this.message.referer
		}
	},
	mounted() {
		this.$emit('contextReady', this.contextRoute)
	},
}
