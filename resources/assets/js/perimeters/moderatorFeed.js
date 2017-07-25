import { createPerimeter } from 'vue-kindergarten';

export default createPerimeter({
	purpose: 'moderatorFeed',

	govern: {
		'can access': function () {
			return this.isAdmin();
		}
	},

	isAdmin() {
		return this.child && this.child.roles.includes('admin');
	}
});
