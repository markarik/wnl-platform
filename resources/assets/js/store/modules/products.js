import { set } from 'vue';
import * as types from 'js/store/mutations-types';

// Initial state
const state = {
	products: [],
};

// Getters
const getters = {
	getCurrentCourseProduct: state => state.products,
	getAlbum: state => state.products,
};

// Mutations
const mutations = {
	[types.SET_PRODUCTS] (state, data) {
		set(state, 'products', data);
	}
};

// Actions
const actions = {
	async fetchCurrent({ commit }) {
		const {data} = await axios.get(getApiUrl('products/current'));
		commit(types.SET_NAVIGATION, data);
	}
};

export default {
	state,
	getters,
	mutations,
	actions
};
