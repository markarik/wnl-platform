import axios from 'axios'
import * as types from 'js/store/mutations-types'
import {set} from 'vue'

import { getApiUrl } from 'js/utils/env'

export const reactionsGetters = {
	getReaction: state => (reactableResource, id, reaction) => state[reactableResource][id][reaction],
}

export const reactionsMutations = {
	[types.SET_REACTION] (state, payload) {
		set(
			state[payload.reactableResource][payload.reactableId],
			payload.reaction,
			{
				hasReacted: payload.hasReacted,
				count: payload.count
			}
		)
	}
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
				.then((response) => resolve(response))
				.catch(error => {
					$wnl.logger.error(error)
					reject()
				})
		})
	},
}
