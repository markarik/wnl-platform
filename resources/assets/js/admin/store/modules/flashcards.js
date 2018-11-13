import _ from 'lodash'
import axios from 'axios'
import { set } from 'vue'
import { getApiUrl } from 'js/utils/env'
import * as types from 'js/admin/store/mutations-types'

// Helper functions

// Namespace
const namespaced = true

// Initial state
const state = {
	ready: false,
	flashcards: [],
}

// Mutations
const mutations = {
	[types.FLASHCARDS_READY] (state) {
		set(state, 'ready', true)
	},
	[types.SETUP_FLASHCARDS] (state, payload) {
		set(state, 'flashcards', payload)
	}
}

// Actions
const actions = {
	async fetchAllFlashcards({commit, getters}) {
		if (_.isEmpty(getters.allFlashcards)) {
			const {data} = await axios.get(getApiUrl('flashcards/all'));
			commit(types.SETUP_FLASHCARDS, data)
		}
	},
	async setup({commit, dispatch}) {
		try {
			await dispatch('fetchAllFlashcards');
			commit(types.FLASHCARDS_READY)
		} catch (error) {
			$wnl.logger.error(error)
		}
	},
}

export default {
	namespaced,
	state,
	mutations,
	actions
}
