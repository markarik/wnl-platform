import axios from 'axios';
import { set } from 'vue';
import { getApiUrl } from 'js/utils/env';
import * as types from 'js/store/mutations-types';

// Namespace
const namespaced = true;

// Initial state
const state = {
	isLoading: false,
	taxonomies: [],
};

// Getters
const getters = {
	taxonomyById: state => id => {
		return state.taxonomies.find(taxonomy => taxonomy.id === id);
	},
};

// Mutations
const mutations = {
	[types.SET_TAXONOMIES_LOADING] (state, payload) {
		set(state, 'isLoading', payload);
	},
	[types.SETUP_TAXONOMIES] (state, payload) {
		set(state, 'taxonomies', payload);
	},
};

// Actions
const actions = {
	async fetchAll({commit}) {
		commit(types.SETUP_TAXONOMIES, []);
		commit(types.SET_TAXONOMIES_LOADING, true);
		try {
			const {data: taxonomies} = await axios.get(getApiUrl('taxonomies/all'));
			commit(types.SETUP_TAXONOMIES, taxonomies);
		} catch (error) {
			throw error;
		} finally {
			commit(types.SET_TAXONOMIES_LOADING, false);
		}
	},
};

export default {
	namespaced,
	state,
	getters,
	mutations,
	actions
};
