import axios from 'axios';
import { set } from 'vue';
import { getApiUrl } from 'js/utils/env';
import * as types from 'js/admin/store/mutations-types';

// Helper functions

// Namespace
const namespaced = true;

// Initial state
const state = {
	ready: false,
	flashcards: [],
};

// Mutations
const mutations = {
	[types.FLASHCARDS_READY] (state, payload) {
		set(state, 'ready', payload);
	},
	[types.SETUP_FLASHCARDS] (state, payload) {
		set(state, 'flashcards', payload);
	}
};

// Actions
const actions = {
	async fetchAllFlashcards({ commit, state }) {
		if (!state.ready) {
			const { data } = await axios.get(getApiUrl('flashcards/all'));
			commit(types.SETUP_FLASHCARDS, data);
		}
	},
	async setup({ commit, dispatch }) {
		try {
			await dispatch('fetchAllFlashcards');
			commit(types.FLASHCARDS_READY, true);
		} catch (error) {
			$wnl.logger.error(error);
		}
	},
	invalidateCache({ commit }) {
		commit(types.FLASHCARDS_READY, false);
	},
};

export default {
	namespaced,
	state,
	mutations,
	actions
};
