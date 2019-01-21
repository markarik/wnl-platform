import axios from 'axios';
import { uniq } from 'lodash';
import { set } from 'vue';
import { getApiUrl } from 'js/utils/env';
import * as types from 'js/admin/store/mutations-types';
import {TAXONOMY_EDITOR_MODES} from 'js/consts/taxonomyTerms';

// Namespace
const namespaced = true;

const initialState = {
	editorMode: TAXONOMY_EDITOR_MODES.ADD,
	expandedTerms: [],
	isLoading: false,
	isSaving: false,
	selectedTerms: [],
	terms: [],
};

const state = {...initialState};

// Getters
const getters = {
	termById: state => id => {
		return state.terms.find(term => term.id === id);
	},
	getAncestorsById: state => id => {
		const ancestors = [];

		let current = state.terms.find(term => term.id === id);
		while (current.parent_id) {
			current = state.terms.find(currentTerm => currentTerm.id === current.parent_id);
			ancestors.unshift(current);
		}

		return ancestors;
	},
	getSiblingsByParentId: state => ({parentId, id}) => {
		return state.terms.filter(stateTerm => stateTerm.parent_id === parentId && stateTerm.id !== id);
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

// Actions
const actions = {
	async fetchTermsByTaxonomy({commit}, taxonomyId) {
		commit(types.RESET_TAXONOMY_TERMS_STATE);
		commit(types.SET_TAXONOMY_TERMS_LOADING, true);
		try {
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
				termsWithOrderNumbers.map(term => includeTag(term, included.tags), termsWithOrderNumbers)
			);
		} catch (error) {
			throw error;
		} finally {
			commit(types.SET_TAXONOMY_TERMS_LOADING, false);
		}
	},

	async create({commit, state}, taxonomyTerm) {
		commit(types.SET_TAXONOMY_TERMS_SAVING, true);
		try {
			const response = await axios.post(getApiUrl('taxonomy_terms?include=tags'), taxonomyTerm);
			const {data: {included, ...term}} = response;
			commit(types.ADD_TERM, includeTag(term, included.tags), state.terms);
			commit(types.REORDER_TERMS, state.terms.filter(listedTerm => listedTerm.parent_id === term.parent_id));
		} catch (error) {
			throw error;
		} finally {
			commit(types.SET_TAXONOMY_TERMS_SAVING, false);
		}
	},

	async update({commit, state}, taxonomyTerm) {
		commit(types.SET_TAXONOMY_TERMS_SAVING, true);
		try {
			const response = await axios.put(getApiUrl(`taxonomy_terms/${taxonomyTerm.id}?include=tags`), taxonomyTerm);
			const {data: {included, ...term}} = response;
			commit(types.UPDATE_TERM, includeTag({...taxonomyTerm, ...term}, included.tags), state.terms);
		} catch (error) {
			throw error;
		} finally {
			commit(types.SET_TAXONOMY_TERMS_SAVING, false);
		}
	},

	async moveTerm({commit, dispatch}, {terms, oldIndex, newIndex}) {
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
		} catch (error) {
			$wnl.logger.error(error);
			dispatch('addAutoDismissableAlert', {
				type: 'error',
				text: 'Nie udało się zapisać zmiany. Odśwież stronę i spróbuj ponownie. Być może Twoje drzewo jest nieaktulane.'
			}, {root: true});
		}
	},

	setEditorMode({commit}, editorMode) {
		commit(types.SET_TAXONOMY_TERM_EDITOR_MODE, editorMode);
	},

	select({commit}, selectedTerms) {
		commit(types.SELECT_TAXONOMY_TERMS, selectedTerms);
	},

	collapse({commit}, termId) {
		commit(types.SET_EXPANDED_TAXONOMY_TERMS, state.expandedTerms.filter(id => id !== termId));
	},
	collapseAll({commit}) {
		commit(types.SET_EXPANDED_TAXONOMY_TERMS, []);
	},

	expand({commit, getters}, termId) {
		const term = getters.termById(termId);

		commit(types.SET_EXPANDED_TAXONOMY_TERMS, uniq([
			...state.expandedTerms,
			...getters.getAncestorsById(term.id).map(ancestor => ancestor.id),
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
