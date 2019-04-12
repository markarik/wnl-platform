import { mapGetters } from 'vuex';

export default {
	computed: {
		...mapGetters(['currentUserName']),
		messageArguments() {
			return {
				currentUserName: {
					description: 'imię użytkownika',
					value: this.currentUserName
				}
			};
		}
	},
};
