import {mapGetters} from 'vuex';

export default {
	computed: {
		...mapGetters(['currentUserName']),
		messageArguments() {
			return {
				currentUserName: this.currentUserName
			};
		}
	},
};
