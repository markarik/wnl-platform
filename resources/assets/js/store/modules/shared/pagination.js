import * as types from 'js/store/mutations-types';
import {set} from 'vue';

const pagination = {
	state () {
		return {
			currentPage: 1,
			lastPage: 1,
			hasMore: false,
			total: 0,
			perPage: 0
		};
	},

	getters: {
		paginationMeta: state => state,
	},

	actions: {
		setPaginationMeta({commit}, {current_page, has_more, last_page, per_page, total}) {
			commit(types.PAGINATION_SET_META, {
				currentPage: current_page,
				hasMore: has_more,
				lastPage: last_page,
				perPage: per_page,
				total
			});
		},
		changePaginationPage({commit}, page) {
			commit(types.PAGINATION_SET_CURRENT_PAGE, {page});
		}
	},

	mutations: {
		[types.PAGINATION_SET_META](state, payload) {
			Object.assign(state, payload);
		},
		[types.PAGINATION_SET_CURRENT_PAGE](state, {page}) {
			set(state, 'currentPage', page);
			set(state, 'hasMore', page === state.lastPage);
		}
	}
};


export default pagination;
