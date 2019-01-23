import {isEmpty} from 'lodash';
import axios from 'axios';
import {set} from 'vue';
import {getApiUrl} from 'js/utils/env';
import * as types from 'js/admin/store/mutations-types';

// Namespace
const namespaced = true;

// Initial state
const state = {
	isLoading: false,
	nodes: [],
};

// Getters
const getters = {};

// Mutations
const mutations = {
	[types.SETUP_COURSE_STRUCTURE](state, payload) {
		set(state, 'nodes', payload);
	},
};

// Actions
const actions = {
	async fetchStructure({commit}, courseId) {
		const response = await axios.get(getApiUrl(`course_structure_nodes/${courseId}?include=lessons,groups`));
		const {data: {included, ...nodes}} = response;
		commit(types.SETUP_COURSE_STRUCTURE, Object.values(nodes).map(node => _parseIncludes(node, included)));
	},
};

const _parseIncludes = (node, included) => {
	if (node.hasOwnProperty('groups')) {
		node.structurable = included.groups[node.groups[0]];
	}
	if (node.hasOwnProperty('lessons')) {
		node.structurable = included.lessons[node.lessons[0]];
	}
	return node;
};

export default {
	namespaced,
	state,
	mutations,
	actions
};
