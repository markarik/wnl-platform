import { createPerimeter } from 'vue-kindergarten';

export default createPerimeter({
	purpose: 'firstEditionParticipant',

	govern: {
		'can access': function () {
			return this.isFirstEditionParticipant();
		}
	},

	isFirstEditionParticipant() {
		return this.child && this.child.roles.includes('edition-1-participant');
	}
});
