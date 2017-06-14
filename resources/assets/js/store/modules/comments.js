import { merge } from 'lodash'
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
	commentProfile: (state) => (id) => state.profiles[id],
}

export const commentsMutations = {
	[types.ADD_COMMENT] (state, payload) {
		let resource = payload.commentableResource,
			resourceId = payload.commentableId,
			comment = payload.comment,
			profile = payload.profile

		if (!state.profiles.hasOwnProperty(profile.id)) {
			set(state.profiles, profile.id, profile)
		}

		set(state.comments, comment.id, comment)
		state[resource][resourceId].comments.push(comment.id)
	},
	[types.REMOVE_COMMENT] (state, payload) {
		let id = payload.id,
			resource = payload.commentableResource,
			resourceId = payload.commentableId,
			comments = _.pull(state[resource][resourceId].comments, id)

		destroy(state.comments, payload.id)
		set(state[resource][resourceId], 'comments', comments)
	},
}

export const commentsActions = {
	addComment({commit}, payload) {
		commit(types.ADD_COMMENT, payload)
	},
	removeComment({commit}, payload) {
		commit(types.REMOVE_COMMENT, payload)
	},
}
