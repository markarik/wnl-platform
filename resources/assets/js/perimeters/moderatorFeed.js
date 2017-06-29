import { createPerimeter } from 'vue-kindergarten';

export default createPerimeter({
	purpose: 'moderatorFeed',

	govern: {
		'can route': function () {
			return this.isAdmin();
		},
		'can viewParagraph': function () {
			return this.isAdmin();
		},
	},

	isAdmin() {
		return this.child.roles.includes('admin');
	}
});