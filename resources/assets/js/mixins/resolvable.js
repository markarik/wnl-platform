import {mapGetters} from 'vuex';
import moderator from 'js/perimeters/moderator'

const resolvable = {
	perimeters: [
		moderator,
	],
	methods: {
		onResolveClick() {
			console.log('hello')
		}
	}
}

export default resolvable;
