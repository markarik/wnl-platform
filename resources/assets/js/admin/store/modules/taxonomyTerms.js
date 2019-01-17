import { isEmpty, uniq } from 'lodash';
import axios from 'axios';
import { set } from 'vue';
import { getApiUrl } from 'js/utils/env';
import * as types from 'js/admin/store/mutations-types';
import {TAXONOMY_EDITOR_MODES} from '../../../consts/taxonomyTerms';

// Namespace
const namespaced = true;

const initialState = {
	editorMode: TAXONOMY_EDITOR_MODES.ADD,
	expandedTerms: [],
	filter: '',
	isLoading: false,
	isSaving: false,
	selectedTerms: [],
	terms: [],
};

const state = Object.assign({}, initialState);

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
	termById: state => id => {
		return state.terms.filter(term => term.id === id)[0];
	}
};

// Mutations
const mutations = {
	[types.RESET_TAXONOMY_TERMS_STATE] (state) {
		Object.keys(state).forEach(key => {
			set(state, key, initialState[key]);
		});
	},
	[types.SET_TAXONOMY_TERMS_LOADING] (state, payload) {
		set(state, 'isLoading', payload);
	},
	[types.SET_TAXONOMY_TERMS_SAVING] (state, payload) {
		set(state, 'isSaving', payload);
	},
	[types.SETUP_TERMS] (state, payload) {
		set(state, 'terms', payload);
	},
	[types.ADD_TERM] (state, payload) {
		state.terms.push(payload);
	},
	[types.UPDATE_TERM] (state, payload) {
		set(state.terms, state.terms.findIndex(term => term.id === payload.id), payload);
	},
	[types.SET_TAXONOMY_TERMS_FILTER] (state, payload) {
		set(state, 'filter', payload);
	},
	[types.SELECT_TAXONOMY_TERMS] (state, payload) {
		set(state, 'selectedTerms', payload);
	},
	[types.SET_EXPANDED_TAXONOMY_TERMS] (state, payload) {
		set(state, 'expandedTerms', payload);
	},
	[types.SET_TAXONOMY_TERM_EDITOR_MODE] (state, payload) {
		set(state, 'editorMode', payload);
	},
	[types.REORDER_TERMS] (state, list) {
		const updatedList = list.map((item, index) => {
			return {
				...item,
				orderNumber: index
			};
		});

		set(state, 'terms', state.terms.map(stateTerm => {
			const updatedTerm = updatedList.find(({id}) => id === stateTerm.id);

			if (updatedTerm) {
				return updatedTerm;
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
		commit(types.RESET_TAXONOMY_TERMS_STATE);
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

	async create({commit, state}, taxonomyTerm) {
		commit(types.SET_TAXONOMY_TERMS_SAVING, true);
		const response = await axios.post(getApiUrl('taxonomy_terms?include=tags'), taxonomyTerm);
		const {data: {included, ...term}} = response;
		commit(types.ADD_TERM, includeAncestors(includeTag(term, included.tags), state.terms));
		commit(types.SET_TAXONOMY_TERMS_SAVING, false);
	},

	async update({commit, state}, taxonomyTerm) {
		commit(types.SET_TAXONOMY_TERMS_SAVING, true);
		const response = await axios.put(getApiUrl(`taxonomy_terms/${taxonomyTerm.id}?include=tags`), taxonomyTerm);
		const {data: {included, ...term}} = response;
		commit(types.UPDATE_TERM, includeAncestors(includeTag(term, included.tags), state.terms));
		commit(types.SET_TAXONOMY_TERMS_SAVING, false);
	},

	setFilter({commit}, filter) {
		commit(types.SET_TAXONOMY_TERMS_FILTER, filter);
	},

	async dragTerm({commit, dispatch}, {terms, oldIndex, newIndex}) {
		const direction = newIndex - oldIndex;

		if (direction === 0) return;

		const term = terms[oldIndex];

		terms.splice(oldIndex, 1);
		terms.splice(newIndex, 0, term);

		commit(types.REORDER_TERMS, terms);

		try {
			await axios.put(getApiUrl('taxonomy_terms/move'), {
				term_id: term.id,
				direction
			});
		} catch (e) {
			$wnl.logger.error(error);
			dispatch('addAlert', {
				type: 'error', text: 'Nie udało się zapisać zmiany. Odśwież stronę i spróbuj ponownie.'
			}, {root: true});
		}
	},

	setEditorMode({commit}, editorMode) {
		commit(types.SET_TAXONOMY_TERM_EDITOR_MODE, editorMode);
	},

	selectTaxonomyTerms({commit}, selectedTerms) {
		commit(types.SELECT_TAXONOMY_TERMS, selectedTerms);
	},

	collapseTaxonomyTerm({commit}, term) {
		commit(types.SET_EXPANDED_TAXONOMY_TERMS, state.expandedTerms.filter(id => id !== term.id));
	},

	expandTaxonomyTerm({commit}, term) {
		commit(types.SET_EXPANDED_TAXONOMY_TERMS, uniq([
			...state.expandedTerms,
			...term.ancestors.map(ancestor => ancestor.id),
			term.id
		]));
	},
};

export default {
	namespaced,
	state,
	mutations,
	getters,
	actions
};
