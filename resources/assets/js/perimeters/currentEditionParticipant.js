import { createPerimeter } from 'vue-kindergarten';
import {SUBSCRIPTION_STATUS} from 'js/consts/user';

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
			this.child.subscription.subscription_status === SUBSCRIPTION_STATUS.ACTIVE;
	},

	isAccountActive() {
		return this.child && !this.child.accountSuspended;
	}
});
