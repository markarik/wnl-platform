import { set } from 'vue';
import * as types from 'js/store/mutations-types';

// Initial state
const state = {
	navigation: {
		breadcrumbs: [],
		items: []
	}
};

// Getters
const getters = {
	breadcrumbs: state => state.navigation.breadcrumbs,
	items: state => state.navigation.items,
	firstItem: state => state.navigation.items[0]
};

// Mutations
const mutations = {
	[types.SET_NAVIGATION] (state, navigationData) {
		set(state, 'navigation', navigationData);
	}
};

// Actions
const actions = {
	setNavigation({ commit }, data) {
		commit(types.SET_NAVIGATION, data);
	}
};

export default {
	state,
	getters,
	mutations,
	actions
};
