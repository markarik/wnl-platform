import axios from 'axios';
import { uniq } from 'lodash';
import { set } from 'vue';
import { getApiUrl } from 'js/utils/env';
import * as types from 'js/admin/store/mutations-types';
import {TAXONOMY_EDITOR_MODES} from '../../../consts/taxonomyTerms';

// Helper functions

// Namespace
const namespaced = true;

// Initial state
const state = {
	editorMode: TAXONOMY_EDITOR_MODES.ADD,
	expandedTerms: [],
	filter: '',
	isLoading: false,
	isSaving: false,
	selectedTerms: [],
	terms: [],
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
	termById: state => id => {
		return state.terms.filter(term => term.id === id)[0];
	}
};

// Mutations
const mutations = {
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
		commit(types.SETUP_TERMS, terms.map(term => includeAncestors(includeTag(term, included.tags), terms)));
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
