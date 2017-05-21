import _ from 'lodash'
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
	return axios.get(getApiUrl(`lessons/${lessonId}?include=qna_questions`))
}

/**
 * @param questionId
 * @returns {Promise}
 * @private
 */
function _getAnswers(questionId) {
	return axios.get(getApiUrl(`qna_questions/${questionId}?include=profiles,qna_answers.profiles,qna_answers.comments`))
}

/**
 * @param answerId
 * @returns {Promise}
 * @private
 */
function _getComments(answerId) {
	return axios.get(getApiUrl(`qna_answers/${answerId}?include=comments.profiles`))
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

const namespaced = true

// Initial state
const state = {
	loading: true,
	questionsIds: [],
	qna_questions: {},
	qna_answers: {},
	comments: {},
	profiles: {},
}

// Getters
const getters = {
	sortedQuestions: state => {
		return state.questionsIds.map((id) => state.qna_questions[id])
	},

	// Resources
	profile: state => (id) => state.profiles[id],
	comment: state => (id) => state.comments[id],

	// Question
	questionContent: state => (id) => state.qna_questions[id].text,
	questionAuthor: (state, getters) => (id) => {
		return getters.profile(state.qna_questions[id].profiles[0])
	},
	questionTimestamp: state => (id) => state.qna_questions[id].created_at,
	questionAnswers: state => (id) => {
		let answersIds = state.qna_questions[id].qna_answers
		if (_.isUndefined(answersIds)) {
			return []
		}

		return answersIds.map((id) => state.qna_answers[id])
	},
	questionAnswersFromLatest: (state, getters) => (id) => {
		return _.reverse(
			_.sortBy(
				getters.questionAnswers(id), (answer) => answer.created_at
			)
		)
	},

	// Answer
	answerComments: state => (id) => {
		let commentsIds = state.qna_answers[id].comments
		if (_.isUndefined(commentsIds)) {
			return []
		}

		return commentsIds.map((id) => state.comments[id])
	},
}

// Mutations
const mutations = {
	[types.IS_LOADING] (state, isLoading) {
		set(state, 'loading', isLoading)
	},
	[types.QNA_SET_QUESTIONS_IDS] (state, questionsIds) {
		set(state, 'questionsIds', questionsIds)
	},
	[types.QNA_UPDATE_QUESTION] (state, payload) {
		let id = payload.questionId, data = payload.data

		set(state.qna_questions, id, _.merge(state.qna_questions[id], data))
	},
	[types.QNA_UPDATE_ANSWER] (state, payload) {
		let id = payload.answerId, data = payload.data

		set(state.qna_answers, id, _.merge(state.qna_answers[id], data))
	},
	[types.UPDATE_INCLUDED] (state, included) {
		_.each(included, (items, resource) => {
			set(state, resource, _.merge(state[resource], items))
		})
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
	fetchQuestions({commit, rootState}) {
		let lessonId = rootState.route.params.lessonId

		// TODO: Error when lessonId is not defined

		return new Promise((resolve, reject) => {
			_getQuestions(lessonId)
				.then((response) => {
					let data = response.data
					if (!_.isUndefined(data.qna_questions)) {
						commit(types.UPDATE_INCLUDED, data.included)
						commit(types.QNA_SET_QUESTIONS_IDS, data.qna_questions)
					}
					resolve()
				})
				.catch((error) => {
					$wnl.logger.error(error)
					reject()
				})
		})
	},
	fetchQuestion({commit}, questionId) {
		return new Promise((resolve, reject) => {
			_getAnswers(questionId)
				.then((response) => {
					let data = response.data,
						included = data.included

					commit(types.UPDATE_INCLUDED, included)
					delete(data.included)
					commit(types.QNA_UPDATE_QUESTION, {questionId, data})
					resolve()
				})
				.catch((error) => {
					$wnl.logger.error(error)
					reject()
				})
		})
	},
	fetchComments({commit}, answerId) {
		return new Promise((resolve, reject) => {
			_getComments(answerId)
				.then((response) => {
					let data = response.data,
						included = data.included

					commit(types.UPDATE_INCLUDED, included)
					delete(data.included)
					commit(types.QNA_UPDATE_ANSWER, {answerId, data})
					resolve()
				})
				.catch((error) => {
					$wnl.logger.error(error)
					reject()
				})
		})
	},

	qnaAddQuestion({commit}, {lessonId, text}){
		return new Promise((resolve, reject) => {
			_postQuestion(lessonId, text).then((response) => {
				if (response.status === 201) {
					_getAnswers(response.data.id).then((response) => {
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
	actions,
	getters,
	mutations,
	namespaced,
	state,
}
