import * as types from 'js/store/mutations-types'
import { mapActions } from 'vuex'

const withChat = {
	methods: {
		...mapActions(['initChat', 'killChat'])
	},

	beforeRouteEnter(from, to, next) {
		next(vm => {
			vm.initChat()
		})
	},

	beforeRouteLeave(from, to, next) {
		this.killChat()
		next();
	}
}

export default withChat;
