import _ from 'lodash'
import axios from 'axios'
import * as types from '../mutations-types'
import {getApiUrl} from 'js/utils/env'
import { set, delete as destroy } from 'vue'
import { reactionsGetters, reactionsMutations, reactionsActions } from 'js/store/modules/reactions'

// API
/**
 * @param lessonId
 * @returns {Promise}
 * @private
 */
function _getQuestions(tags) {
	return new Promise((resolve, reject) => {
		let data = {
			include: 'profiles,reactions,qna_answers.profiles,qna_answers.comments',
			query: {
				hasIn: {
					tags: ['tags.id', tags.map((tag) => tag.id)]
				}
			},
			order: {
				id: 'desc',
			},
		}

		if (tags.length === 0) {
			reject('No tags passed to search for Q&A questions.')
		}

		axios.post(getApiUrl('qna_questions/.search'), data)
			.then((response) => resolve(response))
			.catch((error) => reject(error))
	})
}

function _getQuestionsByIds(ids) {
	return new Promise((resolve, reject) => {
		let data = {
			include: 'profiles,reactions,qna_answers.profiles,qna_answers.comments',
			query: {
				whereIn: ['id', ids],
			},
			order: {
				id: 'desc',
			},
		}

		axios.post(getApiUrl('qna_questions/.search'), data)
			.then((response) => resolve(response))
			.catch((error) => reject(error))
	})
}

function _getQuestionsLatest(limit = 10) {
	return new Promise((resolve, reject) => {
		let data = {
			include: 'tags,profiles,reactions,qna_answers.profiles,qna_answers.comments',
			query: {
				whereDoesntHave: {
					tags: {
						where: [ ['tags.id', '=', 69] ],
					},
				},
			},
			order: {
				id: 'desc',
			},
			limit: [limit, 0]
		}

		axios.post(getApiUrl('qna_questions/.search'), data)
			.then((response) => resolve(response))
			.catch((error) => reject(error))
	})
}

/**
 * @param questionId
 * @returns {Promise}
 * @private
 */
function _getAnswers(questionId) {
	return axios.get(getApiUrl(`qna_questions/${questionId}?include=profiles,qna_answers.profiles,qna_answers.comments,reactions`))
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

function getInitialState() {
	return {
		loading: true,
		questionsIds: [],
		qna_questions: {},
		qna_answers: {},
		comments: {},
		profiles: {},
		tags: {},
	}
}

const namespaced = true

// Initial state
const state = getInitialState()

// Getters
const getters = {
	...reactionsGetters,
	loading: state => state.loading,
	sortedQuestions: state => {
		return _.reverse(
			_.sortBy(
				_.values(state.qna_questions),
				(question) => question.upvote.count
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
	questionTags: state => (id) => {
		let tags = state.qna_questions[id].tags
		if (_.isUndefined(tags)) {
			return []
		}

		return tags.map((id) => state.tags[id])
	},
	questionAnswersFromHighestUpvoteCount: (state, getters) => (id) => {
		return _.reverse(
			_.sortBy(
				getters.questionAnswers(id), (answer) => answer.upvote.count
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
	[types.QNA_SET_QUESTIONS] (state, data) {
		/**
		 * In case you wonder why I destroy it first - please visit.
		 * https://vuejs.org/v2/guide/list.html#Caveats
		 * In short, due to limitations of JS, Vue cannot recognize if an
		 * array updates. The best way to be sure everything is updated
		 * is to destroy the target first using Vue's reactive method
		 * destroy.
		 */
		Object.keys(data).forEach((key) => {
			let question = data[key]
			set(state.qna_questions, question.id, question)
		})
		// set(state, 'questionsIds', questionsIds)
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
			let resourceObject = state[resource]

			_.each(items, (item, index) => {
				set(resourceObject, index, item)
			})
		})
	},
	[types.QNA_DESTROY] (state) {
		let initialState = getInitialState()
		Object.keys(initialState).forEach((field) => {
			set(state, field, initialState[field])
		})
	},
}

// Actions
const actions = {
	...reactionsActions,
	fetchQuestions({commit}, tags) {
		commit(types.IS_LOADING, true)
		// TODO: Error when lessonId is not defined

		return new Promise((resolve, reject) => {
			_getQuestions(tags)
				.then((response) => {
					let data = response.data

					if (!_.isUndefined(data.included)) {
						commit(types.UPDATE_INCLUDED, data.included)
						destroy(data, 'included')
						commit(types.QNA_SET_QUESTIONS, data)
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

	fetchQuestionsByIds({commit}, ids) {
		commit(types.IS_LOADING, true)

		return new Promise((resolve, reject) => {
			_getQuestionsByIds(ids)
				.then((response) => {
					let data = response.data

					if (!_.isUndefined(data.included)) {
						commit(types.UPDATE_INCLUDED, data.included)
						destroy(data, 'included')
						commit(types.QNA_SET_QUESTIONS, data)
					}
					commit(types.IS_LOADING, false)
					resolve()
				})
				.catch((error) => {
					$wnl.logger.error(error)
					commit(types.IS_LOADING, false)
					reject()
				})
		})
	},

	fetchLatestQuestions({commit}, limit = 10) {
		commit(types.IS_LOADING, true)

		return new Promise((resolve, reject) => {
			_getQuestionsLatest(limit)
				.then((response) => {
					let data = response.data

					if (!_.isUndefined(data.included)) {
						commit(types.UPDATE_INCLUDED, data.included)
						destroy(data, 'included')
						commit(types.QNA_SET_QUESTIONS, data)
					}
					commit(types.IS_LOADING, false)
					resolve()
				})
				.catch((error) => {
					$wnl.logger.error(error)
					commit(types.IS_LOADING, false)
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
			resolve()
		})
	},
	destroyQna({commit}) {
		commit(types.QNA_DESTROY)
	},
}

export default {
	actions,
	getters,
	mutations,
	namespaced,
	state,
}
