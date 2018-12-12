import { createPerimeter } from 'vue-kindergarten';

export default createPerimeter({
	purpose: 'upcomingEditionParticipant',

	can: {
		access() {
			return this.isUpcomingEditionParticipant();
		}
	},

	isUpcomingEditionParticipant() {
		return this.child &&
			this.child.subscription &&
			this.child.subscription.status === 'awaiting';
	},
});
