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
	answers: null,
}

function getEmptyAnswers(stateAnswers) {
	return [
		{
			text: '',
			is_correct: false
		},
		{
			text: '',
			is_correct: false
		},
		{
			text: '',
			is_correct: false
		},
		{
			text: '',
			is_correct: false
		},
		{
			text: '',
			is_correct: false
		}
	]
}

// Getters
const getters = {
	questionText: state => state.question ? state.question.text : '',
	questionAnswers: state => state.answers || getEmptyAnswers(),
	questionAnswersMap: state => state.answersMap,
	questionId: state => state.question && state.question.id,
	questionTags: state => state.question ? state.question.tags : [],
	preserveOrder: state => state.question && state.question.preserve_order
}

// Mutations
const mutations = {
	[types.SETUP_QUIZ_QUESTION] (state, data) {
		const answersObject = data.included['quiz_answers'] || {}
		const answersArray = data['quiz_answers'].map(id => answersObject[id])

		set(state, 'question', data)
		set(state, 'answers', answersArray)
		set(state, 'answersMap', answersObject)
	},
	[types.CLEAR_QUIZ_QUESTION] (state, data) {
		set(state, 'answers', getEmptyAnswers())
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
	setupFreshQuestion({ commit }) {
		commit(types.CLEAR_QUIZ_QUESTION)
	}
}

export default {
	namespaced,
	state,
	getters,
	mutations,
	actions
}
