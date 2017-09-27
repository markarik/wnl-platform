import { createPerimeter } from 'vue-kindergarten';

export default createPerimeter({
	purpose: 'moderatorFeed',

	can: {
		access() {
			return this.isModerator()
		}
	},

	isModerator() {
		return this.child && this.child.roles.includes('moderator');
	}
});
