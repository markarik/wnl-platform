const pagination = {
	state () {
		return {
			currentPage: 1,
			lastPage: 1,
			hasMore: false,
			total: 0,
			perPage: 0
		}
	},

	getters: {

	},

	actions: {
		setPaginationMeta(context, payload) {
			console.log(payload, '...payload')
		}
	},

	mutations: {

	}
}


export default pagination;
