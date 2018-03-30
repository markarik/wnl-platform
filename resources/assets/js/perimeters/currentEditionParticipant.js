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
			this.child.roles.includes('moderator')
				|| this.child.roles.includes('admin')
				|| this.child.roles.includes('edition-2-participant')
	}
});
