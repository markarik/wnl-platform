export default {
	methods: {
		emitUserEvent(payload) {
			const eventPayload = {
				...payload,
			};

			this.$emit('userEvent', eventPayload);
		},

		proxyUserEvent(payload) {
			this.$emit('userEvent', payload);
		}
	}
};
