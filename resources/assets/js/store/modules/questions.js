import { set } from 'vue'
import * as types from '../mutations-types'
import {getApiUrl} from 'js/utils/env'
import axios from 'axios'

const namespaced = true

// Initial state
const state = {
	questions: {},
	loading: true
}

// Getters
const getters = {
	questions: state => state.questions,
}

// Mutations
const mutations = {
	[types.QUESTIONS_SET] (state, payload) {
		set(state, 'questions', payload)
	}
}

// Actions
const actions = {
	getQuestions({commit}) {
		return _fetchAllQuestions()
			.then(({data}) => {
				commit(types.QUESTIONS_SET, data)
			})
	}
}


const _fetchAllQuestions = () => {
	return axios.get(getApiUrl('questions'))
}

export default {
	namespaced,
	state,
	getters,
	mutations,
	actions
}
