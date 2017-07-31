import { createPerimeter } from 'vue-kindergarten';

export default createPerimeter({
	purpose: 'moderatorFeed',

	govern: {
		'can access': function () {
			return this.isModerator();
		}
	},

	isModerator() {
		return this.child && this.child.roles.includes('moderator');
	}
});
