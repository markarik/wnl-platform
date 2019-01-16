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
	[types.MOVE_TERM] (state, {term, replaceWith}) {
		const replaceWithOrderNumber = replaceWith.orderNumber;
		const termOrderNumber = term.orderNumber;

		set(state, 'terms', state.terms.map(stateTerm => {
			if (stateTerm.id === term.id) {
				return {
					...term,
					orderNumber: replaceWithOrderNumber
				};
			}

			if (stateTerm.id === replaceWith.id) {
				return {
					...replaceWith,
					orderNumber: termOrderNumber
				};
			}

			return stateTerm;
		}));
	}
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
		const orderNumbers = {};
		const termsWithOrderNumbers = terms.map(term => {
			if (typeof orderNumbers[term.parent_id] === 'undefined') {
				orderNumbers[term.parent_id] = 0;
			} else {
				orderNumbers[term.parent_id] = orderNumbers[term.parent_id] + 1;
			}

			term.orderNumber = orderNumbers[term.parent_id];

			return term;
		});

		commit(
			types.SETUP_TERMS,
			termsWithOrderNumbers.map(term => includeAncestors(includeTag(term, included.tags), termsWithOrderNumbers))
		);
		commit(types.SET_TAXONOMY_TERMS_LOADING, false);
	},

	async create({commit}, taxonomyTerm) {
		const response = await axios.post(getApiUrl('taxonomy_terms?include=tags'), taxonomyTerm);
		const {data: {included, ...term}} = response;
		commit(types.ADD_TERM, includeTag(term, included.tags));
	},

	setFilter({commit}, filter) {
		commit(types.SET_TAXONOMY_TERMS_FILTER, filter);
	},

	async moveTerm({commit, state}, {term, direction}) {
		const newIndex = term.orderNumber + direction;
		const replaceWith = state.terms.find(sibling => sibling.parent_id === term.parent_id && sibling.orderNumber === newIndex);

		commit(types.MOVE_TERM, {
			term,
			replaceWith,
		});

		await axios.put(getApiUrl('taxonomy_terms/move'), {
			term: term.id,
			direction
		});
	}
};

export default {
	namespaced,
	state,
	mutations,
	getters,
	actions
};
