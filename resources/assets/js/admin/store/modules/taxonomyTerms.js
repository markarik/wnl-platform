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
	filter: '',
};

// Getters
const getters = {
	filteredTerms: state => {
		if (state.filter === '') {
			return state.terms;
		} else {
			// TODO make filtering smart to include parents even when they don't match pattern
			return state.terms.filter(term => term.tag.name.toLocaleLowerCase().includes(state.filter.toLocaleLowerCase()));
		}
	},
};

// Mutations
const mutations = {
	[types.SET_TAXONOMY_TERMS_LOADING] (state, payload) {
		set(state, 'isLoading', payload);
	},
	[types.SETUP_TERMS] (state, payload) {
		set(state, 'terms', payload);
	},
	[types.ADD_TERM] (state, payload) {
		state.terms.push(payload);
	},
	[types.SET_TAXONOMY_TERMS_FILTER] (state, payload) {
		set(state, 'filter', payload);
	},
};

const includeTag = (term, tags) => {
	term.tag = tags[term.tags[0]];
	return term;
};

const includeAncestors = (term, terms) => {
	term.ancestors = [];

	let current = term;
	while (current.parent_id) {
		current = terms.find(currentTerm => currentTerm.id === current.parent_id);
		term.ancestors.unshift(current);
	}

	return term;
};

// Actions
const actions = {
	async fetchTermsByTaxonomy({commit}, taxonomyId) {
		commit(types.SETUP_TERMS, []);
		commit(types.SET_TAXONOMY_TERMS_LOADING, true);
		const response = await axios.get(getApiUrl(`taxonomy_terms/byTaxonomy/${taxonomyId}?include=tags`));
		const {data: {included, ...termsObj}} = response;
		const terms = Object.values(termsObj);
		commit(types.SETUP_TERMS, terms.map(term => includeAncestors(includeTag(term, included.tags), terms)));
		commit(types.SET_TAXONOMY_TERMS_LOADING, false);
	},

	async create({commit}, taxonomyTerm) {
		const response = await axios.post(getApiUrl('taxonomy_terms?include=tags'), taxonomyTerm);
		const {data: {included, ...term}} = response;
		commit(types.ADD_TERM, includeTag(term, included.tags));
	},

	setFilter({commit}, filter) {
		commit(types.SET_TAXONOMY_TERMS_FILTER, filter);
	}
};

export default {
	namespaced,
	state,
	mutations,
	getters,
	actions
};
