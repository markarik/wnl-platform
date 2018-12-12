import axios from 'axios';
import * as types from 'js/store/mutations-types';
import {set} from 'vue';

import { getApiUrl } from 'js/utils/env';

const defaultValue = {
	hasReacted: false,
	count: 0
};

export const convertToReactable = (instance) => {
	return {
		...instance,
		upvote: defaultValue,
		downvote: defaultValue,
		bookmark: defaultValue,
		thanks: defaultValue
	};
};

export const reactionsGetters = {
	getReaction: state => (reactableResource, id, reaction) => state[reactableResource][id][reaction] || defaultValue,
};

export const reactionsMutations = {
	[types.SET_REACTION] (state, payload) {
		set(
			state[payload.reactableResource][payload.reactableId],
			payload.reaction,
			{
				hasReacted: payload.hasReacted,
				count: payload.count
			}
		);
	},
};

export const reactionsActions = {
	setReaction({commit, dispatch}, payload) {
		return new Promise((resolve, reject) => {
			let data = {
					'reactable_resource' : payload.reactableResource,
					'reactable_id'       : payload.reactableId,
					'reaction_type'      : payload.reaction,
				},
				// hasReacted should represent new state
	 			method = payload.hasReacted ? 'delete' : 'post',
				params = payload.hasReacted ? { params: data } : [data];

			return axios[method](getApiUrl('reactions'), params)
				.then((response) => {
					const hasReacted = !payload.hasReacted;
					const count = hasReacted ? payload.count + 1 : payload.count - 1;

					commit(types.SET_REACTION, {
						count,
						hasReacted,
						reactableResource: payload.reactableResource,
						reactableId: payload.reactableId,
						reaction: payload.reaction,
					});

					resolve(response);
				})
				.catch(reject);
		});
	},

	markManyAsReacted({commit}, payload) {
		if (payload.length === 0) {
			return Promise.resolve();
		}

		return new Promise((resolve, reject) => {
			const serializedPayload = payload.map((reaction) => {
				const hasReacted = true;
				const count = reaction.count + 1;

				commit(types.SET_REACTION, {
					count,
					hasReacted,
					reactableResource: reaction.reactableResource,
					reactableId: reaction.reactableId,
					reaction: reaction.reaction,
				});

				return {
					'reactable_resource' : reaction.reactableResource,
					'reactable_id'       : reaction.reactableId,
					'reaction_type'      : reaction.reaction,
				};
			});

			return axios.post(getApiUrl('reactions'), serializedPayload)
				.then((response) => resolve(response))
				.catch(error => {
					$wnl.logger.error(error);
					reject();
				});
		});
	},
};
