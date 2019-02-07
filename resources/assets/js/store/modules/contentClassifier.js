import axios from 'axios';
import {set} from 'vue';

import {CONTENT_TYPES, CONTENT_TYPE_TO_RESOURCE_ROUTE} from 'js/consts/contentClassifier';
import {getApiUrl} from 'js/utils/env';
import {parseTaxonomyTermsFromIncludes} from 'js/utils/contentClassifier';
import * as mutationsTypes from 'js/store/mutations-types';
import {REQUEST_STATES} from 'js/consts/state';

const INCLUDE = 'taxonomy_terms.tag,taxonomy_terms.taxonomy,taxonomy_terms.ancestors.tag';

const initialState = {
	[CONTENT_TYPES.ANNOTATION]: {},
	[CONTENT_TYPES.QUIZ_QUESTION]: {},
	[CONTENT_TYPES.SLIDE]: {},
	[CONTENT_TYPES.FLASHCARD]: {},
	recentTaxonomyId: null,
};

const state = {
	...initialState
};

const getters = {
	getContentItem: state => ({contentItemType, contentItemId}) => state[contentItemType][contentItemId] &&
		state[contentItemType][contentItemId].data,
	getContentItemState: state => ({contentItemType, contentItemId}) => state[contentItemType][contentItemId] &&
		state[contentItemType][contentItemId].state,
	canAccess: (state, getters, rootState, rootGetters) => rootGetters.isAdmin || rootGetters.isModerator
};

const mutations = {
	[mutationsTypes.CONTENT_CLASSIFIER_ATTACH_TERM](state, {term, contentItem}) {
		const stateTerms = state[contentItem.type][contentItem.id].data.taxonomyTerms;
		if (stateTerms.findIndex(taxonomyTerm => taxonomyTerm.id === term.id) < 0) {
			stateTerms.push(term);
		}
	},
	[mutationsTypes.CONTENT_CLASSIFIER_DETACH_TERM](state, {term, contentItem}) {
		const stateTerms = state[contentItem.type][contentItem.id].data.taxonomyTerms;
		const index = stateTerms.findIndex(taxonomyTerm => taxonomyTerm.id === term.id);
		if (index > -1) {
			stateTerms.splice(index, 1);
		}
	},
	[mutationsTypes.CONTENT_CLASSIFIER_SET_LOADING](state, {contentItemIds, contentType}) {
		const reducedState = contentItemIds.reduce((items, id) => {
			items[id] = {
				state: REQUEST_STATES.LOADING,
				data: null,
			};
			return items;
		}, {});

		set(state, contentType, {
			...state[contentType],
			...reducedState
		});
	},
	[mutationsTypes.CONTENT_CLASSIFIER_SET_CONTENT](state, {contentItems, contentType}) {
		const reducedState = contentItems.reduce((items, item) => {
			items[item.id] = {
				state: REQUEST_STATES.LOADED,
				data: item,
			};
			return items;
		}, {});

		set(state, contentType, {
			...state[contentType],
			...reducedState
		});
	},
	[mutationsTypes.CONTENT_CLASSIFIER_SET_ERROR](state, {contentItemIds, contentType}) {
		const reducedState = contentItemIds.reduce((items, id) => {
			items[id] = {
				state: REQUEST_STATES.ERROR,
				data: null,
			};
			return items;
		}, {});

		set(state, contentType, {
			...state[contentType],
			...reducedState
		});
	},
	[mutationsTypes.CONTENT_CLASSIFIER_SET_RECENT_TAXONOMY_ID](state, taxonomyId) {
		set(state, 'recentTaxonomyId', taxonomyId);
	},
};

const actions = {
	async fetchTaxonomyTerms({commit, getters}, {contentType, contentIds}) {
		if (!getters.canAccess) return;
		if (!contentIds.length) return;

		commit(mutationsTypes.CONTENT_CLASSIFIER_SET_LOADING, {contentItemIds: contentIds, contentType});

		const url = getApiUrl(`${CONTENT_TYPE_TO_RESOURCE_ROUTE[contentType]}/.filter`);
		try {
			const {data} = await axios.post(url, {
				include: INCLUDE,
				filters: [
					{
						by_ids: {ids: contentIds}
					},
				],
				// todo - figure out something here
				limit: 10000
			});
			const {data: {included = {}, ...contentItems}} = data;
			const parsedContentItems = Object.values(contentItems).map(item => {
				item.type = contentType;
				item.taxonomyTerms = parseTaxonomyTermsFromIncludes(item.taxonomy_terms, included);
				return item;
			});

			commit(mutationsTypes.CONTENT_CLASSIFIER_SET_CONTENT, {contentItems: parsedContentItems, contentType});
		} catch (error) {
			$wnl.logger.capture(error);
			commit(mutationsTypes.CONTENT_CLASSIFIER_SET_ERROR, {contentItemIds: contentIds, contentType});
		}
	},
	setRecentTaxonomyId({commit}, id) {
		commit(mutationsTypes.CONTENT_CLASSIFIER_SET_RECENT_TAXONOMY_ID, id);
	},
};

export default {
	state,
	getters,
	mutations,
	actions,
	namespaced: true
};
