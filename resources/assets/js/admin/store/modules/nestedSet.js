import axios from 'axios';
import {uniq} from 'lodash';
import {set} from 'vue';
import {getApiUrl} from 'js/utils/env';
import * as types from 'js/admin/store/mutations-types';
import {NESTED_SET_EDITOR_MODES} from 'js/consts/nestedSet';

// Namespace
const namespaced = true;

export const initialState = {
	editorMode: NESTED_SET_EDITOR_MODES.ADD,
	expandedTerms: [],
	isLoading: false,
	isSaving: false,
	selectedTerms: [],
	terms: [],
};

// Getters
export const nestedSetGetters = {
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
	getChildrenByParentId: state => (parentId) => {
		return state.terms
			.filter(stateTerm => stateTerm.parent_id === parentId)
			.sort((termA, termB) => termA.orderNumber - termB.orderNumber);
	}
};

// Mutations
export const nestedSetMutations = {
	[types.RESET_NESTED_SET_STATE](state) {
		Object.keys(state).forEach(key => {
			set(state, key, initialState[key]);
		});
	},
	[types.SET_NESTED_SET_LOADING](state, payload) {
		set(state, 'isLoading', payload);
	},
	[types.SET_NESTED_SET_SAVING](state, payload) {
		set(state, 'isSaving', payload);
	},
	[types.SETUP_NESTED_SET](state, payload) {
		set(state, 'terms', payload);
	},
	[types.ADD_NESTED_SET_NODE](state, payload) {
		state.terms.push(payload);
	},
	[types.UPDATE_NESTED_SET_NODE](state, payload) {
		set(state.terms, state.terms.findIndex(term => term.id === payload.id), payload);
	},
	[types.DELETE_NESTED_SET_NODE](state, payload) {
		const index = state.terms.findIndex(term => term.id === payload.id);
		state.terms.splice(index, 1);
	},
	[types.SELECT_NESTED_SET](state, payload) {
		set(state, 'selectedTerms', payload);
	},
	[types.SET_EXPANDED_NESTED_SET](state, payload) {
		set(state, 'expandedTerms', payload);
	},
	[types.SET_NESTED_SET_EDITOR_MODE](state, payload) {
		set(state, 'editorMode', payload);
	},
	// the order of items in passed list is important!
	[types.UPDATE_NESTED_SET_ORDER_NUMBERS](state, {list}) {
		const updatedList = list
			.map((item, index) => ({
				...item,
				orderNumber: index
			}));

		set(state, 'terms', state.terms.map(stateTerm => {
			const updatedTerm = updatedList.find(({id}) => id === stateTerm.id);

			if (updatedTerm) {
				return updatedTerm;
			}

			return stateTerm;
		}));
	}
};

// Actions
export const nestedSetActions = {
	async setUpNestedSet({commit, dispatch}, setId) {
		commit(types.RESET_NESTED_SET_STATE);
		commit(types.SET_NESTED_SET_LOADING, true);
		let nodes = await dispatch('_fetch', setId);
		try {
			const orderNumbers = {};
			const termsWithOrderNumbers = nodes.map(term => {
				if (typeof orderNumbers[term.parent_id] === 'undefined') {
					orderNumbers[term.parent_id] = 0;
				} else {
					orderNumbers[term.parent_id] = orderNumbers[term.parent_id] + 1;
				}
				term.orderNumber = orderNumbers[term.parent_id];

				return term;
			});
			commit(types.SETUP_NESTED_SET, termsWithOrderNumbers);
		} catch (error) {
			throw error;
		} finally {
			commit(types.SET_NESTED_SET_LOADING, false);
		}
	},
	async create({commit, state, getters, dispatch}, nodeData) {
		commit(types.SET_NESTED_SET_SAVING, true);
		try {
			let node = await dispatch('_post', nodeData);
			commit(types.ADD_NESTED_SET_NODE, node);
			commit(types.UPDATE_NESTED_SET_ORDER_NUMBERS, {
				list: getters.getChildrenByParentId(node.parent_id)
			});
		} catch (error) {
			throw error;
		} finally {
			commit(types.SET_NESTED_SET_SAVING, false);
		}
	},

	async update({commit, state, getters, dispatch}, nodeData) {
		commit(types.SET_NESTED_SET_SAVING, true);
		try {
			let node = await dispatch('_put', nodeData);
			const {parent_id: originalParentId} = getters.termById(node.id);
			const {parent_id: updatedParentId} = node;

			if (originalParentId !== updatedParentId) {
				node.orderNumber = getters.getChildrenByParentId(updatedParentId).length;
			}

			commit(types.UPDATE_NESTED_SET_NODE, {...nodeData, ...node});
		} catch (error) {
			throw error;
		} finally {
			commit(types.SET_NESTED_SET_SAVING, false);
		}
	},

	async delete({commit, state, getters, dispatch}, node) {
		commit(types.SET_NESTED_SET_SAVING, true);
		try {
			await dispatch('_delete', node);

			commit(types.DELETE_NESTED_SET_NODE, node);
			commit(types.UPDATE_NESTED_SET_ORDER_NUMBERS, {
				list: getters.getChildrenByParentId(node.parent_id)
			});
		} catch (error) {
			throw error;
		} finally {
			commit(types.SET_NESTED_SET_SAVING, false);
		}
	},

	async moveTerm({dispatch, commit}, {term, direction}) {
		commit(types.SET_NESTED_SET_SAVING, true);

		try {
			dispatch('reorderSiblings', {term, direction});
			dispatch('_move', {term, direction});
		} catch (e) {
			throw e;
		} finally {
			commit(types.SET_NESTED_SET_SAVING, false);
		}
	},

	reorderSiblings({getters, commit}, {term, direction}) {
		const allSiblings = getters.getChildrenByParentId(term.parent_id);
		const oldIndex = allSiblings.findIndex(sibling => sibling.id === term.id);
		const newIndex = oldIndex + direction;

		if (newIndex < 0 || newIndex > allSiblings.length - 1) {
			throw new Error('out of range');
		}

		allSiblings.splice(oldIndex, 1);
		allSiblings.splice(newIndex, 0, term);

		commit(types.UPDATE_NESTED_SET_ORDER_NUMBERS, {list: allSiblings});
	},

	setEditorMode({commit}, editorMode) {
		commit(types.SET_NESTED_SET_EDITOR_MODE, editorMode);
	},

	select({commit}, selectedTerms) {
		commit(types.SELECT_NESTED_SET, selectedTerms);
	},

	collapse({commit, state}, termId) {
		commit(types.SET_EXPANDED_NESTED_SET, state.expandedTerms.filter(id => id !== termId));
	},
	collapseAll({commit}) {
		commit(types.SET_EXPANDED_NESTED_SET, []);
	},

	expand({commit, getters, state}, termId) {
		const term = getters.termById(termId);

		commit(types.SET_EXPANDED_NESTED_SET, uniq([
			...state.expandedTerms,
			...getters.getAncestorsById(term.id).map(ancestor => ancestor.id),
			term.id
		]));
	},
};
