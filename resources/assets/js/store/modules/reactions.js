import axios from 'axios'
import * as types from 'js/store/mutations-types'

import { getApiUrl } from 'js/utils/env'

export const reactionsGetters = {
	getReaction: state => (reactableResource, id, reaction) => state[reactableResource][id][reaction],
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
					commit(types[`${payload.module.toUpperCase()}_SET_REACTION`], {
						...payload,
						hasReacted: !payload.hasReacted
					})
					resolve(response)
				})
				.catch(error => {
					$wnl.logger.error(error)
					reject()
				})
		})
	},
}
