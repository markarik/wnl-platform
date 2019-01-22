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
	[types.SETUP_NODES](state, payload) {
		set(state, 'nodes', payload);
	},
};

// Actions
const actions = {
	async fetchStructure({commit}, courseId) {
		try {
			const response = await axios.get(getApiUrl(`course_structure_nodes/${courseId}?include=lessons,groups`));
			const {data: {included, ...nodes}} = response;
			commit(types.SETUP_NODES, Object.values(nodes).map(node => _parseIncludes(node, included)));
		} catch (error) {
			$wnl.logger.capture(error);
		}
	},
};

const _parseIncludes = (node, included) => {
	return node;
};

export default {
	namespaced,
	state,
	mutations,
	actions
};
