import { set } from 'vue';
import * as types from 'js/store/mutations-types';
import {getApiUrl} from 'js/utils/env';
import {PRODUCTS_SLUGS} from 'js/consts/products';

const namespaced = true;

// Initial state
const state = {
	products: [],
};

// Getters
const getters = {
	getCurrentCourseProductSignupsOpen: state => {
		const currentProduct = state.products.find(item => item.slug === PRODUCTS_SLUGS.SLUG_ONLINE);
		return currentProduct && currentProduct.signups_open;
	},
	getAlbum: state => state.products.find(item => item.slug === PRODUCTS_SLUGS.SLUG_ALBUM),
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
		try{
			const {data} = await axios.get(getApiUrl('products/current/all'));
			commit(types.SET_PRODUCTS, data);
		} catch (e) {
			$wnl.logger.capture(e);
		}
	}
};

export default {
	namespaced,
	state,
	getters,
	mutations,
	actions
};
