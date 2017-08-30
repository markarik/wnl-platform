import _ from 'lodash'
import axios from 'axios'
import { set } from 'vue'
import { getApiUrl } from 'js/utils/env'
import { resource } from 'js/utils/config'
import * as types from 'js/admin/store/mutations-types'

// Helper functions

// Namespace
const namespaced = false

// Initial state
const state = {
	question: null,
	questionId: null,
	answers: null
}

// Getters
const getters = {
	questionText: state => state.question && state.question.text,
	questionAnswers: state => state.answers,
	questionId: state => state.question && state.question.id
}

// Mutations
const mutations = {
	[types.SETUP_QUIZ_QUESTION] (state, data) {
		const answersObject = data.included['quiz_answers']
		const answersArray = Object.keys(answersObject).map(key => answersObject[key])

		set(state, 'question', data)
		set(state, 'answers', answersArray)
	}
}

// Actions
const actions = {
	getQuizQuestion({ commit, getters }, id) {
		axios.get(getApiUrl(`quiz_questions/${id}?include=quiz_answers`))
			.then((response) => {
				commit(types.SETUP_QUIZ_QUESTION, response.data)
			})
	},
	saveAnswers({ commit }, answers) {
		const promises = answers.map(
			answer => axios.put(
				getApiUrl(`quiz_answers/${answer.id}`),
				{ text: answer.text, is_correct: answer.isCorrect }
			)
		)

		return Promise.all(promises)
	},
}

export default {
	namespaced,
	state,
	getters,
	mutations,
	actions
}
