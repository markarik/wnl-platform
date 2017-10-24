const pagination = {
	defaultModuleState() {
		return {
			currentPage: 1,
			lastPage: 1,
			hasMore: false,
			total: 0,
			perPage: 0
		}
	},

	state () {
		return {
			modules: {}
		}
	},

	getters: {

	},

	actions: {
		setMeta(context, payload) {
		}
	},

	mutations: {

	},

	namespaced: true
}


export default pagination;
