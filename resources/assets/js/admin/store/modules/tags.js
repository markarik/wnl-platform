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
	tags: [],
};

// Mutations
const mutations = {
	[types.SET_TAGS_LOADING] (state, payload) {
		set(state, 'isLoading', payload);
	},
	[types.SETUP_TAGS] (state, payload) {
		set(state, 'tags', payload);
	},
};

// Actions
const actions = {
	async fetchAll({commit}) {
		commit(types.SETUP_TAGS, []);
		commit(types.SET_TAGS_LOADING, true);
		const {data: tags} = await axios.get(getApiUrl(`tags/all`));
		commit(types.SETUP_TAGS, tags);
		commit(types.SET_TAGS_LOADING, false);
	},
};

export default {
	namespaced,
	state,
	mutations,
	actions
};
