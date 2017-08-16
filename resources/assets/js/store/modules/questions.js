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
	getQuestionsList: state => Object.values(state.questions)
}

// Mutations
const mutations = {
	[types.QUESTIONS_SET] (state, payload) {
		// TODO endpoint could return data in shape: {id: data, ...}
		const serialized = {};
		payload.forEach(question => {
			serialized[question.id] = {
				...question
			}
		})
		set(state, 'questions', serialized)
	},
	[types.QUESTIONS_SET_ANSWER] (state, {id, included}) {
		if (included.quiz_answers) {
			const answers = Object.values(included.quiz_answers).map((answer) => answer)
			set(state.questions[id], 'answers', answers)
		}
	}
}

// Actions
const actions = {
	fetchQuestions({commit}) {
		return _fetchAllQuestions()
			.then(({data}) => {
				commit(types.QUESTIONS_SET, data)
			})
	},
	fetchQuestionAnswers({commit}, id) {
		return _fetchQuestionAnswers(id)
			.then(({data}) => {
				commit(types.QUESTIONS_SET_ANSWER, data)
			})
	}
}


const _fetchAllQuestions = () => {
	return axios.get(getApiUrl('questions'))
}

const _fetchQuestionAnswers = (id) => {
	return axios.get(getApiUrl(`quiz_questions/${id}?include=quiz_answers`))
}

export default {
	namespaced,
	state,
	getters,
	mutations,
	actions
}
