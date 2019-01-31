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
	expandedNodes: [],
	isLoading: false,
	isSaving: false,
	selectedNodes: [],
	nodes: [],
};

// Getters
export const nestedSetGetters = {
	nodeById: state => id => {
		return state.nodes.find(node => node.id === id);
	},
	getAncestorsById: state => id => {
		const ancestors = [];

		let current = state.nodes.find(node => node.id === id);
		while (current.parent_id) {
			current = state.nodes.find(currentNode => currentNode.id === current.parent_id);
			ancestors.unshift(current);
		}

		return ancestors;
	},
	getChildrenByParentId: state => (parentId) => {
		return state.nodes
			.filter(stateNode => stateNode.parent_id === parentId)
			.sort((nodeA, nodeB) => nodeA.orderNumber - nodeB.orderNumber);
	},
	getRootNodes: (state, getters) => getters.getChildrenByParentId(null),
	currentNode: (state, getters) => {
		if (state.selectedNodes.length === 0) return null;
		return getters.nodeById(state.selectedNodes[0]);
	},
	getParentNode: (state, getters) => node => {
		return getters.getAncestorsById(node.id).slice(-1)[0];
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
		set(state, 'nodes', payload);
	},
	[types.ADD_NESTED_SET_NODE](state, payload) {
		state.nodes.push(payload);
	},
	[types.UPDATE_NESTED_SET_NODE](state, payload) {
		set(state.nodes, state.nodes.findIndex(node => node.id === payload.id), payload);
	},
	[types.DELETE_NESTED_SET_NODE](state, payload) {
		const index = state.nodes.findIndex(node => node.id === payload.id);
		state.nodes.splice(index, 1);
	},
	[types.SELECT_NESTED_SET](state, payload) {
		set(state, 'selectedNodes', payload);
	},
	[types.SET_EXPANDED_NESTED_SET](state, payload) {
		set(state, 'expandedNodes', payload);
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

		set(state, 'nodes', state.nodes.map(stateNode => {
			const updatedNode = updatedList.find(({id}) => id === stateNode.id);

			if (updatedNode) {
				return updatedNode;
			}

			return stateNode;
		}));
	}
};

// Actions
export const nestedSetActions = {
	async setUpNestedSet({commit, dispatch}, setId) {
		commit(types.RESET_NESTED_SET_STATE);
		commit(types.SET_NESTED_SET_LOADING, true);
		const nodes = await dispatch('_fetch', setId);
		try {
			const orderNumbers = {};
			const nodesWithOrderNumbers = nodes.map(node => {
				if (typeof orderNumbers[node.parent_id] === 'undefined') {
					orderNumbers[node.parent_id] = 0;
				} else {
					orderNumbers[node.parent_id] = orderNumbers[node.parent_id] + 1;
				}
				node.orderNumber = orderNumbers[node.parent_id];

				return node;
			});
			commit(types.SETUP_NESTED_SET, nodesWithOrderNumbers);
		} catch (error) {
			throw error;
		} finally {
			commit(types.SET_NESTED_SET_LOADING, false);
		}
	},
	async create({commit, state, getters, dispatch}, nodeData) {
		commit(types.SET_NESTED_SET_SAVING, true);
		try {
			const node = await dispatch('_post', nodeData);
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
			const node = await dispatch('_put', nodeData);
			const {parent_id: originalParentId} = getters.nodeById(node.id);
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

	async moveNode({dispatch, commit}, {node, direction}) {
		commit(types.SET_NESTED_SET_SAVING, true);

		try {
			dispatch('reorderSiblings', {node, direction});
			dispatch('_move', {node, direction});
		} catch (e) {
			throw e;
		} finally {
			commit(types.SET_NESTED_SET_SAVING, false);
		}
	},

	reorderSiblings({getters, commit}, {node, direction}) {
		const allSiblings = getters.getChildrenByParentId(node.parent_id);
		const oldIndex = allSiblings.findIndex(sibling => sibling.id === node.id);
		const newIndex = oldIndex + direction;

		if (newIndex < 0 || newIndex > allSiblings.length - 1) {
			throw new Error('out of range');
		}

		allSiblings.splice(oldIndex, 1);
		allSiblings.splice(newIndex, 0, node);

		commit(types.UPDATE_NESTED_SET_ORDER_NUMBERS, {list: allSiblings});
	},

	setEditorMode({commit}, editorMode) {
		commit(types.SET_NESTED_SET_EDITOR_MODE, editorMode);
	},

	select({commit}, selectedNodes) {
		commit(types.SELECT_NESTED_SET, selectedNodes);
	},

	collapse({commit, state}, nodeId) {
		commit(types.SET_EXPANDED_NESTED_SET, state.expandedNodes.filter(id => id !== nodeId));
	},

	collapseAll({commit}) {
		commit(types.SET_EXPANDED_NESTED_SET, []);
	},

	expandAll({commit, state}) {
		commit(types.SET_EXPANDED_NESTED_SET, state.nodes.map(node => node.id));
	},

	expand({commit, getters, state}, nodeId) {
		const node = getters.nodeById(nodeId);

		commit(types.SET_EXPANDED_NESTED_SET, uniq([
			...state.expandedNodes,
			...getters.getAncestorsById(node.id).map(ancestor => ancestor.id),
			node.id
		]));
	},
};
