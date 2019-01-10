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
	ready: false,
	flashcardsSets: [],
};

// Mutations
const mutations = {
	[types.FLASHCARDS_SETS_READY] (state, payload) {
		set(state, 'ready', payload);
	},
	[types.SETUP_FLASHCARDS_SETS] (state, payload) {
		set(state, 'flashcardsSets', payload);
	}
};

// Actions
const actions = {
	async fetchAllFlashcardsSets({commit, state}) {
		if (!state.ready) {
			const { data } = await axios.get(getApiUrl('flashcards_sets/all'));
			commit(types.SETUP_FLASHCARDS_SETS, data);
		}
	},
	async setup({commit, dispatch}) {
		try {
			await dispatch('fetchAllFlashcardsSets');
			commit(types.FLASHCARDS_SETS_READY, true);
		} catch (error) {
			$wnl.logger.error(error);
		}
	},
	invalidateCache({commit}) {
		commit(types.FLASHCARDS_SETS_READY, false);
	}
};

export default {
	namespaced,
	state,
	mutations,
	actions
};
