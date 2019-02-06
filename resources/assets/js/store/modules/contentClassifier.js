import axios from 'axios';
import {set} from 'vue';

import {CONTENT_TYPES, CONTENT_TYPE_TO_RESOURCE_ROUTE} from 'js/consts/contentClassifier';
import {getApiUrl} from 'js/utils/env';
import {parseTaxonomyTermsFromIncludes} from 'js/utils/contentClassifier';
import * as mutationsTypes from 'js/store/mutations-types';

const INCLUDE = 'taxonomy_terms.tags,taxonomy_terms.taxonomies,taxonomy_terms.ancestors.tags';

const initialState = {
	[CONTENT_TYPES.ANNOTATION]: {},
	[CONTENT_TYPES.QUIZ_QUESTION]: {},
	[CONTENT_TYPES.SLIDE]: {},
	[CONTENT_TYPES.FLASHCARD]: {}
};

const state = {
	...initialState
};

const getters = {
	getContentItem: state => ({contentItemType, contentItemId}) => state[contentItemType][contentItemId],
	canAccess: (state, getters, rootState, rootGetters) => rootGetters.isAdmin || rootGetters.isModerator
};

const mutations = {
	[mutationsTypes.CONTENT_CLASSIFIER_ATTACH_TERM](state, {term, contentItem}) {
		if (contentItem.taxonomyTerms.findIndex(taxonomyTerm => taxonomyTerm.id === term.id) < 0) {
			contentItem.taxonomyTerms.push(term);
		}
	},
	[mutationsTypes.CONTENT_CLASSIFIER_DETACH_TERM](state, {term, contentItem}) {
		const index = contentItem.taxonomyTerms.findIndex(taxonomyTerm => taxonomyTerm.id === term.id);
		if (index > -1) {
			contentItem.taxonomyTerms.splice(index, 1);
		}
	},
	[mutationsTypes.CONTENT_CLASSIFIER_SET_CONTENT](state, {contentItems, contentType}) {
		const reducedState = contentItems.reduce((items, item) => {
			items[item.id] = item;
			return items;
		}, {});

		set(state, contentType, {
			...state[contentType],
			...reducedState
		});
	}
};

const actions = {
	async fetchTaxonomyTerms({commit, getters}, {contentType, contentIds}) {
		if (!getters.canAccess) return;

		const url = getApiUrl(`${CONTENT_TYPE_TO_RESOURCE_ROUTE[contentType]}/.filter`);
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
	}
};

export default {
	state,
	getters,
	mutations,
	actions,
	namespaced: true
};
