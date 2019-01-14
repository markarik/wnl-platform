import { isEmpty } from 'lodash';
import axios from 'axios';
import { set } from 'vue';
import { getApiUrl } from 'js/utils/env';
import * as types from 'js/admin/store/mutations-types';

// Helper functions

// Namespace
const namespaced = true;

// Initial state
const state = {
	isLoading: false,
	terms: [],
};

// Mutations
const mutations = {
	[types.SET_TAXONOMY_TERMS_LOADING] (state, payload) {
		set(state, 'isLoading', payload);
	},
	[types.SETUP_TERMS] (state, payload) {
		set(state, 'terms', payload);
	}
};

// Actions
const actions = {
	async fetchTermsByTaxonomy({commit, state}, taxonomyId) {
		commit(types.SETUP_TERMS, []);
		commit(types.SET_TAXONOMY_TERMS_LOADING, true);
		const response = await axios.get(getApiUrl(`taxonomy_terms/byTaxonomy/${taxonomyId}?include=tags`));
		const {data: {included: {tags}, ...terms}} = response;
		commit(types.SETUP_TERMS, Object.values(terms).map(term => {
			term.tag = tags[term.tags[0]];
			return term;
		}));
		commit(types.SET_TAXONOMY_TERMS_LOADING, false);
	},
};

export default {
	namespaced,
	state,
	mutations,
	actions
};
