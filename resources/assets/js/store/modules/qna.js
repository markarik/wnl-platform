import axios from 'axios'
import * as types from '../mutations-types'
import {getApiUrl} from 'js/utils/env'
import {set} from 'vue'

// API
/**
 * @param lessonId
 * @private
 */
function _getQuestions(lessonId) {
	return axios.get(getApiUrl(`lessons/${lessonId}?include=tags,questions.answers.users,questions.users`))
}

/**
 * @param lessonId
 * @private
 */
function _postQuestion(lessonId) {

}

/**
 * @param questionId
 * @private
 */
function _postAnswer(questionId) {

}

// Initial state
const state = {
	questions: {},
}

// Getters
const getters = {
	qnaGetQuestions: state => (lessonId) => {
		try {
			return state.questions[lessonId].included.questions
		}
		catch (e) {
			return false
		}
	},
	qnaGetUser: state => (lessonId, userId) => {
		return state.questions[lessonId].included.users[userId]
	},
	qnaGetAnswers: state => (lessonId, ids) => {
		return state.questions[lessonId].included.answers.filter(answer => ids.indexof(answer.id) !== -1)
	}

}

// Mutations
const mutations = {
	[types.QNA_SET_QUESTIONS] (state, payload) {
		set(state.questions, payload.lessonId, payload.data)
	},
}

// Actions
const actions = {
	/**
	 * @param commit
	 * @param lessonId
	 * @returns {Promise}
	 */
	qnaSetQuestions({commit}, lessonId) {
		return new Promise((resolve, reject) => {
			_getQuestions(lessonId).then((response) => {
				commit(types.QNA_SET_QUESTIONS, {lessonId, data: response.data})
				resolve()
			})
		})
	}
}

export default {
	state,
	getters,
	mutations,
	actions
}
