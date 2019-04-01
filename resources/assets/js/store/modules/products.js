import { set } from 'vue';
import * as types from 'js/store/mutations-types';
import {getApiUrl} from 'js/utils/env';


const namespaced = true;

// Initial state
const state = {
	products: [],
};

// Getters
const getters = {
	getCurrentCourseProduct: state => state.products.find(item => item.slug === 'wnl-online'),
	getAlbum: state => state.products.find(item => item.slug === 'wnl-album'),
};

// Mutations
const mutations = {
	[types.SET_PRODUCTS] (state, data) {
		set(state, 'products', data);
	}
};

// Actions
const actions = {
	async fetchCurrentProducts({ commit }) {
		const {data} = await axios.get(getApiUrl('products/current/all'));
		commit(types.SET_PRODUCTS, data);
	}
};

export default {
	namespaced,
	state,
	getters,
	mutations,
	actions
};
