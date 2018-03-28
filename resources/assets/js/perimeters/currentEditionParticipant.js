import { createPerimeter } from 'vue-kindergarten';

export default createPerimeter({
	purpose: 'currentEditionParticipant',

	can: {
		access() {
			return this.isCurrentEditionParticipant()
		}
	},

	isCurrentEditionParticipant() {
		return this.child &&
			this.child.subscription_status === 'active'
	},
});
