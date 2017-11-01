import { createPerimeter } from 'vue-kindergarten';

export default createPerimeter({
	purpose: 'firstEditionParticipant',

	can: {
		access() {
			return this.isFirstEditionParticipant()
		}
	},

	isFirstEditionParticipant() {
		return this.child &&
			(this.child.roles.includes('moderator') ||
				this.child.roles.includes('admin')) ||
			(this.child.roles.includes('edition-1-participant') &&
				this.child.roles.includes('edition-2-participant'))
	}
});
