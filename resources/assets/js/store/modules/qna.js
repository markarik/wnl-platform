import axios from 'axios'
import * as types from '../mutations-types'
import {getApiUrl} from 'js/utils/env'
import {mockData} from 'js/store/modules/qnaMockData'
import {set} from 'vue'

// API
/**
 * @param lessonId
 * @returns {Promise}
 * @private
 */
function _getQuestions(lessonId) {
	return axios.get(getApiUrl(`lessons/${lessonId}?include=tags,questions.answers.users,questions.users`))
}

/**
 * @param questionId
 * @returns {Promise}
 * @private
 */
function _getQuestion(questionId) {
	return axios.get(getApiUrl(`questions/${questionId}?include=users,answers.users`))
}

/**
 * @param lessonId
 * @param text
 * @returns {Promise}
 * @private
 */
function _postQuestion(lessonId, text) {
	return axios.post(getApiUrl(`questions`), {lessonId, text})
}

/**
 * @param questionId
 * @param text
 * @returns {Promise}
 * @private
 */
function _postAnswer(questionId, text) {
	return axios.post(getApiUrl(`answers`), {questionId, text})
}

/**
 * @param answerId
 * @returns {Promise}
 * @private
 */
function _getAnswer(answerId) {
	return axios.get(getApiUrl(`answers/${answerId}?include=users`))
}

// Initial state
const state = {
	questions: {},
}

// Getters
const getters = {
	qnaGetMockData: state => (lessonId) => {
		return mockData[lessonId]
	},
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
		let answers = {}
		ids.forEach((id) => {
			answers[id] = state.questions[lessonId].included.answers[id]
		})
		return answers
	}
}

// Mutations
const mutations = {
	[types.QNA_SET_QUESTIONS] (state, payload) {
		set(state.questions, payload.lessonId, payload.data)
	},
	[types.QNA_ADD_QUESTION] (state, payload) {
		set(state.questions[payload.lessonId].included.questions, payload.data.id, payload.data)
	},
	[types.QNA_ADD_ANSWER] (state, payload) {
		set(state.questions[payload.lessonId].included.answers, payload.data.id, payload.data)
		state.questions[payload.lessonId].included.questions[payload.questionId].answers.push(payload.data.id)
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
		return new Promise((resolve) => {
			_getQuestions(lessonId).then((response) => {
				commit(types.QNA_SET_QUESTIONS, {lessonId, data: response.data})
				resolve()
			})
		})
	},
	qnaAddQuestion({commit}, {lessonId, text}){
		return new Promise((resolve, reject) => {
			_postQuestion(lessonId, text).then((response) => {
				if (response.status === 201) {
					_getQuestion(response.data.id).then((response) => {
						commit(types.QNA_ADD_QUESTION, {lessonId, data: response.data})
						resolve()
					})
				} else {
					reject()
				}
			})
		})
	},
	qnaAddAnswer({commit}, {lessonId, questionId, text}){
		return new Promise((resolve, reject) => {
			_postAnswer(questionId, text).then((response) => {
				if (response.status === 201) {
					_getAnswer(response.data.id).then((response) => {
						commit(types.QNA_ADD_ANSWER, {lessonId, questionId, data: response.data})
						resolve()
					})
				} else {
					reject()
				}
			})
		})
	},
}

export default {
	state,
	getters,
	mutations,
	actions
}
