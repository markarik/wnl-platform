import { merge } from 'lodash'
import axios from 'axios'
import { set, delete as destroy } from 'vue'

import * as types from 'js/store/mutations-types'
import { getApiUrl } from 'js/utils/env'
import { getModelByResource, modelToResourceMap } from 'js/utils/config'

function _fetchComments(ids, model) {
	if (!model) {
		return Promise.reject('Model not defined')
	}

	let data = {
		query: {
			where: [
				['commentable_type', model],
			],
			whereIn: ['commentable_id', ids],
		},
		order: {
			id: 'asc',
		},
		include: 'profiles',
	}

	return axios.post(getApiUrl('comments/.search'), data)
}

function _resolveComment(id) {
	return axios.put(getApiUrl(`comments/${id}`), {
		resolve: true,
		text: 'bla bla'
	})
}

export const commentsGetters = {
	/**
	 * [getComments description]
	 * @param  {Object} commentable { commentable_resource: String, commentable_id: Int }
	 */
	comments: (state) => (payload) => {
		if (typeof state[payload.resource][payload.id] !== 'object' ||
			!state[payload.resource][payload.id].hasOwnProperty('comments')
		) {
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
		if (!state[resource][resourceId].hasOwnProperty('comments')) {
			set(state[resource][resourceId], 'comments', [])
		}
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
	[types.RESOLVE_COMMENT] (state, payload) {
		let id = payload.id,
			resource = payload.commentableResource,
			resourceId = payload.commentableId,
			comments = state[resource][resourceId].comments.filter(comment => comment !== id)

		destroy(state.comments, payload.id)
		set(state[resource][resourceId], 'comments', comments)
	},
	[types.SET_COMMENTS] (state, payload) {
		set(state, 'profiles', payload.included.profiles)
		destroy(payload, 'included')

		_.each(payload, (comment, index) => {
			let resource = modelToResourceMap[comment.commentable_type],
				resourceId = comment.commentable_id

			set(state.comments, comment.id, comment)

			!state[resource][resourceId].comments.includes(comment.id)
				&& state[resource][resourceId].comments.push(comment.id)
		})
	}
}

export const commentsActions = {
	addComment({commit}, payload) {
		commit(types.ADD_COMMENT, payload)
	},
	removeComment({commit}, payload) {
		commit(types.REMOVE_COMMENT, payload)
	},
	resolveComment({commit}, payload) {
		_resolveComment(payload.id)
			.then(() => commit(types.RESOLVE_COMMENT, payload))
	},
	fetchComments({commit}, {ids, resource}) {
		return new Promise((resolve, reject) => {
			const model = getModelByResource(resource)

			_fetchComments(ids, model)
				.then((response) => {
					if (!response.data.hasOwnProperty('included')) {
						return resolve()
					}

					commit(types.SET_COMMENTS, response.data)
					resolve()
				})
				.catch((error) => {
					$wnl.logger.error(error)
					reject()
				})
		})
	}
}
