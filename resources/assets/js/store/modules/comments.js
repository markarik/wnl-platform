import axios from 'axios'
import { set, delete as destroy } from 'vue'

import * as types from 'js/store/mutations-types'
import { getApiUrl } from 'js/utils/env'

export const commentsGetters = {
	/**
	 * [getComments description]
	 * @param  {Object} commentable { commentable_resource: String, commentable_id: Int }
	 */
	comments: (state) => (payload) => {
		if (!state[payload.resource][payload.id].hasOwnProperty('comments')) {
			return []
		}

		return state[payload.resource][payload.id].comments.map((commentId) => state.comments[commentId])
	},
	commentProfile: (state) => (id) => state.profiles[id]
}

export const commentsMutations = {
	[types.ADD_COMMENT] () {
		console.log('Mutation ADD_COMMENT!')
	},
	[types.REMOVE_COMMENT] (state, payload) {
		let id = payload.id,
			resource = payload.commentable_resource,
			resourceId = payload.commentable_id,
			comments = _.pull(state[resource][resourceId].comments, id)

		destroy(state.comments, payload.id)
		set(state[resource][resourceId], 'comments', comments)
	},
}

export const commentsActions = {
	addComment({commit}, payload) {
		console.log('Action addComment!')
	},
	removeComment({commit}, payload) {
		commit(types.REMOVE_COMMENT, payload)
	},
}
//
// export default {
// 	commentsGetters,
// 	commentsMutations,
// 	commentsActions,
// }
