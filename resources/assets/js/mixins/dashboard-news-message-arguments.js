import {mapGetters} from 'vuex';

export default {
	data() {
		return {
			messageArgumentsDescription: {
				currentUserName: 'imię użytkownika',
			}
		};
	},
	computed: {
		...mapGetters(['currentUserName']),
		messageArguments() {
			return {
				currentUserName: this.currentUserName
			};
		}
	},
};
