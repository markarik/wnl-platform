import _ from 'lodash'
import axios from 'axios'
import * as types from '../mutations-types'
import {getApiUrl} from 'js/utils/env'
import { set, delete as destroy } from 'vue'

// API
/**
 * @param lessonId
 * @returns {Promise}
 * @private
 */
function _getQuestions(lessonId) {
	return axios.get(getApiUrl(`lessons/${lessonId}?include=qna_questions.profiles`))
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
	loading: state => state.loading,
	sortedQuestions: state => {
		return _.reverse(
			_.sortBy(
				state.questionsIds.map((id) => state.qna_questions[id]),
				(question) => question.created_at,
			)
		)
	},

	// Resources
	getQuestion: state => (id) => state.qna_questions[id],
	answer:      state => (id) => state.qna_answers[id],
	profile:     state => (id) => state.profiles[id],
	comment:     state => (id) => state.comments[id],

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
		/**
		 * In case you wonder why I destroy it first - please visit.
		 * https://vuejs.org/v2/guide/list.html#Caveats
		 * In short, due to limitations of JS, Vue cannot recognize if an
		 * array updates. The best way to be sure everything is updated
		 * is to destroy the target first using Vue's reactive method
		 * destroy.
		 */
		destroy(state, 'questionsIds')
		set(state, 'questionsIds', questionsIds)
	},
	[types.QNA_UPDATE_QUESTION] (state, payload) {
		let id = payload.questionId,
			data = _.merge(state.qna_questions[id], payload.data)

		/**
		 * In case you wonder why I destroy it first - please visit.
		 * https://vuejs.org/v2/guide/list.html#Caveats
		 * In short, due to limitations of JS, Vue cannot recognize if an
		 * array updates. The best way to be sure everything is updated
		 * is to destroy the target first using Vue's reactive method
		 * destroy.
		 */
		destroy(state.qna_questions, id)
		set(state.qna_questions, id, data)
	},
	[types.QNA_REMOVE_QUESTION] (state, payload) {
		let id = payload.questionId,
			questionsIds = _.pull(state.questionsIds, id)

		destroy(state.qna_questions, id)
		set(state, 'questionsIds', questionsIds)
	},
	[types.QNA_UPDATE_ANSWER] (state, payload) {
		let id = payload.answerId,
			data = _.merge(state.qna_answers[id], payload.data)

		/**
		 * In case you wonder why I destroy it first - please visit.
		 * https://vuejs.org/v2/guide/list.html#Caveats
		 * In short, due to limitations of JS, Vue cannot recognize if an
		 * array updates. The best way to be sure everything is updated
		 * is to destroy the target first using Vue's reactive method
		 * destroy.
		 */
		destroy(state.qna_answers, id)
		set(state.qna_answers, id, _.merge(state.qna_answers[id], data))
	},
	[types.QNA_REMOVE_ANSWER] (state, payload) {
		let id = payload.answerId,
			questionId = payload.questionId,
			answers = _.pull(state.qna_questions[questionId].qna_answers, id)

		destroy(state.qna_answers, id)
		set(state.qna_questions, 'qna_answers', answers)
	},
	[types.QNA_REMOVE_COMMENT] (state, payload) {
		let id = payload.commentId,
			answerId = payload.answerId,
			comments = _.pull(state.qna_answers[answerId].comments, id)

		destroy(state.comments, id)
		set(state.qna_answers, 'comments', comments)
	},
	[types.UPDATE_INCLUDED] (state, included) {
		_.each(included, (items, resource) => {
			let merged = _.merge(state[resource], items)
			destroy(state, resource)
			set(state, resource, merged)
		})
	},
}

// Actions
const actions = {
	fetchQuestions({commit, rootState}) {
		let lessonId = rootState.route.params.lessonId

		commit(types.IS_LOADING, true)
		// TODO: Error when lessonId is not defined

		return new Promise((resolve, reject) => {
			_getQuestions(lessonId)
				.then((response) => {
					let data = response.data
					if (!_.isUndefined(data.qna_questions)) {
						commit(types.UPDATE_INCLUDED, data.included)
						commit(types.QNA_SET_QUESTIONS_IDS, data.qna_questions)
					}
					commit(types.IS_LOADING, false)
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
	removeQuestion({commit}, questionId) {
		return new Promise((resolve, reject) => {
			commit(types.QNA_REMOVE_QUESTION, {questionId})
			resolve()
		})
	},
	removeAnswer({commit}, payload) {
		return new Promise((resolve, reject) => {
			commit(types.QNA_REMOVE_ANSWER, {
				questionId: payload.questionId,
				answerId: payload.answerId,
			})
			resolve()
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
	removeComment({commit}, payload) {
		return new Promise((resolve, reject) => {
			commit(types.QNA_REMOVE_COMMENT, {
				answerId: payload.answerId,
				commentId: payload.commentId,
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
