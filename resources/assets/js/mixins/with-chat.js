import { mapActions } from 'vuex';

const withChat = {
	methods: {
		...mapActions(['initChat', 'killChat'])
	},

	beforeRouteEnter(to, from, next) {
		next(vm => {
			vm.initChat();
		});
	},

	beforeRouteLeave(to, from, next) {
		this.killChat();
		next();
	}
};

export default withChat;
