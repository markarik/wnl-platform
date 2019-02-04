import { isEmpty } from 'lodash';
import axios from 'axios';
import { set } from 'vue';
import { getApiUrl } from 'js/utils/env';
import * as types from 'js/admin/store/mutations-types';

// Namespace
const namespaced = true;

// Initial state
const state = {
	isLoading: false,
	groups: [],
};

// Mutations
const mutations = {
	[types.SET_GROUPS_LOADING] (state, payload) {
		set(state, 'isLoading', payload);
	},
	[types.SETUP_GROUPS] (state, payload) {
		set(state, 'groups', payload);
	},
	[types.ADD_GROUP] (state, payload) {
		state.groups.push(payload);
	},
};

// Actions
const actions = {
	async fetchAll({commit}) {
		commit(types.SETUP_GROUPS, []);
		commit(types.SET_GROUPS_LOADING, true);
		try {
			const {data: groups} = await axios.get(getApiUrl('groups/all'));
			commit(types.SETUP_GROUPS, groups);
		} catch (error) {
			throw error;
		} finally {
			commit(types.SET_GROUPS_LOADING, false);
		}
	},
	async create({commit}, name) {
		const {data: group} = await axios.post(getApiUrl('groups'), {
			name
		});
		commit(types.ADD_GROUP, group);

		return group;
	}
};

export default {
	namespaced,
	state,
	mutations,
	actions
};
