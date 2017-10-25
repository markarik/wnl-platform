import * as types from '../../mutations-types'
import {set, delete as destroy} from 'vue'

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
		paginationMeta: state => state
	},

	actions: {
		setPaginationMeta({commit}, {current_page, has_more, last_page, per_page, total}) {
			commit('PAGINATION_SET_META', {
				currentPage: current_page,
				hasMore: has_more,
				lastPage: last_page,
				perPage: per_page,
				total
			})
		}
	},

	mutations: {
		[types.PAGINATION_SET_META](state, payload) {
			set(state, payload)
		}
	}
}


export default pagination;
