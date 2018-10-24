export default {
	methods: {
		emitUserEvent(payload) {
			const eventPayload = {
				feature: this.feature,
				feature_component: this.feature_component,
				...payload,
			}

			this.$emit('userEvent', eventPayload)
		},

		proxyUserEvent(payload) {
			this.$emit('userEvent', payload)
		}
	}
}
