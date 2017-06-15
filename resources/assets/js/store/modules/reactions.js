import axios from 'axios'
import * as types from 'js/store/mutations-types'

import { getApiUrl } from 'js/utils/env'

export const reactionsGetters = {
  getReaction: state => (reactableResource, id, reaction) => state[reactableResource][id][reaction],
}

export const reactionsMutations = {
  [types.SET_REACTION] (state, payload) {
		let resource = payload.reactableResource,
				resourceId = payload.reactableId,
				reaction = payload.reaction,
				designatedObject = state[resource][resourceId][reaction]

		designatedObject.hasReacted ? designatedObject.count-- : designatedObject.count++,

		designatedObject.hasReacted = !designatedObject.hasReacted
	},
}

export const reactionsActions = {
  setReaction({commit}, payload) {
		return new Promise((resolve, reject) => {
			let data = {
					'reactable_resource' : payload.reactableResource,
					'reactable_id'       : payload.reactableId,
					'reaction_type'      : payload.reaction,
				},
	 			method = payload.hasReacted ? 'delete' : 'post',
				params = payload.hasReacted ? { params: data } : data

			return axios[method](getApiUrl(`reactions`), params)
				.then((response) => {
					if (!payload.preventUpdate) {
						commit(types.SET_REACTION, payload)
					}
					resolve(response)
				})
				.catch(error => $wnl.logger.error(error))
					reject()
		})
	},
}
