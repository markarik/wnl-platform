import { createPerimeter } from 'vue-kindergarten';

export default createPerimeter({
	purpose: 'currentEditionParticipant',

	can: {
		access() {
			return this.isCurrentEditionParticipant() && this.isAccountActive();
		}
	},

	isCurrentEditionParticipant() {
		return this.child &&
			this.child.subscription &&
			this.child.subscription.status === 'active';
	},

	isAccountActive() {
		return this.child && !this.child.accountSuspended;
	}
});
