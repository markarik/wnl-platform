import axios from 'axios';
import { set, delete as destroy } from 'vue';
import { each, uniqBy } from 'lodash';

import * as types from 'js/store/mutations-types';
import { getApiUrl } from 'js/utils/env';
import { getModelByResource, modelToResourceMap } from 'js/utils/config';
import {reactionsGetters, reactionsMutations, reactionsActions, convertToReactable} from 'js/store/modules/reactions';

function _fetchComments({...commentsQuery}) {
	if (!commentsQuery.hasOwnProperty('commentable_type')) {
		return Promise.reject('Commentable type not defined');
	}

	if (!commentsQuery.hasOwnProperty('commentable_id')) {
		return Promise.reject('Commentable id not defined');
	}

	const requestParams = {
		commentable_type: commentsQuery.commentable_type,
		commentable_id: commentsQuery.commentable_id,
		include: 'profiles,reactions'
	};

	if (commentsQuery.hasOwnProperty('comment_id')) {
		requestParams.id = commentsQuery.comment_id;
	}
	return axios.get(getApiUrl('comments/query'), {params: requestParams})
		.then((data) => {
			return data;
		});
}

function _resolveComment(id, status = true) {
	return axios.put(getApiUrl(`comments/${id}`), {
		resolved: status
	});
}

export const commentsState = {
	drafts: {}
};

export const commentsGetters = {
	...reactionsGetters,
	/**
	 * [getComments description]
	 * @param  {Object} commentable { commentable_resource: String, commentable_id: Int }
	 */
	comments: (state) => (payload) => {
		if (typeof state[payload.resource][payload.id] !== 'object' ||
			!state[payload.resource][payload.id].hasOwnProperty('comments')
		) {
			return [];
		}

		return state[payload.resource][payload.id].comments.map((commentId) => state.comments[commentId]);
	},
	commentProfile: (state) => (id) => state.profiles[id],
};

export const commentsMutations = {
	...reactionsMutations,
	[types.ADD_COMMENT] (state, payload) {
		let resource = payload.commentableResource,
			resourceId = payload.commentableId,
			comment = payload.comment,
			profile = payload.profile;

		if (!state.profiles.hasOwnProperty(profile.id)) {
			set(state.profiles, profile.id, profile);
		}

		set(state.comments, comment.id, comment);
		if (!state[resource][resourceId].hasOwnProperty('comments')) {
			set(state, resource, {
				...state[resource],
				[resourceId]: {
					...state[resource][resourceId],
					comments: [
						comment.id
					]
				}
			});
		} else {
			state[resource][resourceId].comments.push(comment.id);
		}
	},
	[types.REMOVE_COMMENT] (state, payload) {
		let resource = payload.commentableResource,
			resourceId = payload.commentableId,
			comments = state[resource][resourceId].comments.filter(commentId => {
				return Number(commentId) !== Number(payload.id);
			});

		destroy(state.comments, payload.id);
		set(state[resource][resourceId], 'comments', comments);
	},
	[types.RESOLVE_COMMENT] (state, payload) {
		const id = payload.id,
			comment = state.comments[payload.id],
			resource = payload.commentableResource,
			resourceId = payload.commentableId,
			comments = state[resource][resourceId].comments.map(comment => comment.id === id ? {...comment, resolved: true} : comment);

		set(state.comments, payload.id, {...comment, resolved: true});
		set(state[resource][resourceId], 'comments', comments);
	},
	[types.UNRESOLVE_COMMENT] (state, payload) {
		const id = payload.id,
			comment = state.comments[payload.id],
			resource = payload.commentableResource,
			resourceId = payload.commentableId,
			comments = state[resource][resourceId].comments.map(comment => comment.id === id ? {...comment, resolved: false} : comment);

		set(state.comments, payload.id, {...comment, resolved: false});
		set(state[resource][resourceId], 'comments', comments);
	},
	[types.SET_COMMENTABLE_COMMENTS] (state, comments) {
		const commentsResourceObj = {};

		each(comments, (comment) => {
			let resource = modelToResourceMap[comment.commentable_type],
				resourceId = comment.commentable_id;

			if (!commentsResourceObj[resource]) {
				commentsResourceObj[resource] = {};
			}

			if (!commentsResourceObj[resource][resourceId]) {
				commentsResourceObj[resource][resourceId] = {};
			}

			commentsResourceObj[resource][resourceId][comment.id] = true;
		});

		Object.keys(commentsResourceObj).forEach(resource => {
			Object.keys(commentsResourceObj[resource]).forEach(resourceId => {
				const oldComments = state[resource][resourceId].comments || [];
				const commentsIds = uniqBy(
					oldComments.concat(Object.keys(commentsResourceObj[resource][resourceId])),
					commentId => commentId.toString()
				);
				set(state[resource][resourceId], 'comments', commentsIds);
			});
		});
	},
	[types.SET_COMMENTS] (state, payload) {
		set(state, 'comments', {
			...state.comments, ...payload
		});
	},
	[types.SET_COMMENTS_PROFILES] (state, payload) {
		set(state, 'profiles', {
			...state.profiles, ...payload
		});
	},
	[types.SET_COMMENTS_COMMENTABLE_COMMENT_DRAFT] (state, {commentableResource, content}) {
		set(state.drafts, commentableResource, content);
	}
};

export const commentsActions = {
	...reactionsActions,
	addComment({commit, dispatch}, payload) {
		const {comment} = payload;
		const withReaction = convertToReactable(comment);
		dispatch('comments/setComments', {[withReaction.id]: withReaction}, {root:true});
		commit(types.ADD_COMMENT, payload);
	},
	removeComment({commit}, payload) {
		commit(types.REMOVE_COMMENT, payload);
	},
	resolveComment({commit}, payload) {
		_resolveComment(payload.id)
			.then(() => commit(types.RESOLVE_COMMENT, payload));
	},
	unresolveComment({commit}, payload) {
		_resolveComment(payload.id, false)
			.then(() => commit(types.UNRESOLVE_COMMENT, payload));
	},
	setComments({commit}, {included, ...comments}) {
		commit(types.SET_COMMENTS, comments);
	},
	setProfiles({commit}, payload) {
		commit(types.SET_COMMENTS_PROFILES, payload);
	},
	async setupComments({dispatch}, {resource, ...args}) {
		const model = getModelByResource(resource);
		try {
			await dispatch('fetchComments', {commentable_type: model, ...args});
		} catch (e) {
			$wnl.logger.error(e);
		}
	},
	async fetchComments({commit, dispatch}, {...args}) {
		const response = await _fetchComments({...args});
		if (!response.data.hasOwnProperty('included')) {
			return;
		}

		const {included, ...comments} = response.data;
		const serializedComments = {};
		Object.values(comments).map(comment => {
			serializedComments[comment.id] = comment;
		});

		commit(types.SET_COMMENTS_PROFILES, included.profiles);
		commit(types.SET_COMMENTS, serializedComments);
		dispatch('comments/setComments', serializedComments, {
			root: true
		});
		commit(types.SET_COMMENTABLE_COMMENTS, comments);

		return comments;
	},
};

export default {
	actions: commentsActions,
	mutations: commentsMutations,
	getters: commentsGetters,
	state: commentsState,
	namespaced: true
};
