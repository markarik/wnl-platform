import { createPerimeter } from 'vue-kindergarten';
import { SUBSCRIPTION_STATUS } from 'js/consts/user';

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
			this.child.subscription.subscription_status === SUBSCRIPTION_STATUS.AWAITING;
	},
});
