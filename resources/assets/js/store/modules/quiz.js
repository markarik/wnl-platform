import axios from 'axios'
import store from 'store'
import _ from 'lodash'
import { set, delete as destroy } from 'vue'
import { useLocalStorage, getApiUrl } from 'js/utils/env'
import { resource } from 'js/utils/config'
import { commentsGetters, commentsMutations, commentsActions } from 'js/store/modules/comments'
import * as types from 'js/store/mutations-types'

const CACHE_VERSION = 2

function getLocalStorageKey(setId, userSlug) {
	return `wnl-quiz-${setId}-u-${userSlug}-${CACHE_VERSION}`
}

function fetchQuizSet(id) {
	return axios.get(
		getApiUrl(`quiz_sets/${id}?include=quiz_questions.quiz_answers,quiz_questions.comments.profiles`)
	)
}

/**
 * Calculates a percentage share of a value in total
 * @param  {Integer} value
 * @param  {Integer} total
 * @return {Integer} Returns an integer being a percentage value
 */
function getPercentageShare(value, total) {
	return _.toInteger(value*100/total)
}

// Should the module be namespaced?
const namespaced = true

// Initial state
const state = {
	attempts: [],
	comments: {},
	isComplete: false,
	loaded: false,
	questionsIds: [],
	questionsLength: 0,
	quiz_answers: {},
	quiz_questions: {},
	processing: false,
	profiles: {},
	setId: null,
	setName: '',
}

/*
question: {
	answers: array,
	attempts: int,
	comments: array,
	isResolved: bool,
	selectedAnswer: int (original index of answer),
	text: string,
}
*/

const getters = {
	...commentsGetters,
	getAnswers: (state) => (questionId) => {
		return state.quiz_questions[questionId].quiz_answers.map(
			(answerId) => state.quiz_answers[answerId]
		)
	},
	getAttempts: (state) => state.attempts,
	getComments: (state) => (id) => {
		return state.quiz_questions[id].comments.map((commentId) => state.comments[commentId])
	},
	getCurrentScore: (state, getters) => {
		return _.round(getters.getResolved.length * 100 / state.questionsLength, 0)
	},
	getQuestions: (state) => state.questionsIds.map((id) => state.quiz_questions[id]),
	getResolved: (state, getters) => _.filter(getters.getQuestions, {'isResolved': true}),
	getSelectedAnswer: (state, getters) => (id) => {
		return state.quiz_questions[id].selectedAnswer
	},
	getUnresolved: (state, getters) => _.filter(getters.getQuestions, {'isResolved': false}),
	getUnanswered: (state, getters) => _.filter(
		getters.getQuestions, (question) => _.isNull(question.selectedAnswer)
	),
	isComplete: (state) => state.isComplete,
	isLoaded: (state) => state.loaded,
	isProcessing: (state) => state.processing,
	isResolved: (state) => (index) => state.quiz_questions[index].isResolved,
}

const mutations = {
	...commentsMutations,
	[types.QUIZ_ATTEMPT] (state, payload) {
		state.attempts.push(payload)
	},
	[types.QUIZ_COMPLETE] (state) {
		set(state, 'isComplete', true)
	},
	[types.QUIZ_IS_LOADED] (state) {
		set(state, 'loaded', true)
	},
	[types.QUIZ_RESET_ANSWER] (state, payload) {
		set(state.quiz_questions[payload.id], 'selectedAnswer', null)
	},
	[types.QUIZ_RESOLVE_QUESTION] (state, payload) {
		set(state.quiz_questions[payload.id], 'isResolved', true)
	},
	[types.QUIZ_RESTORE_STATE] (state, payload) {
		_.forEach(payload, (value, key) => {
			set(state, key, value)
		})
	},
	[types.QUIZ_SET_QUESTIONS] (state, payload) {
		set(state, 'questionsIds', payload.questionsIds)
		set(state, 'setId', payload.setId)
		set(state, 'setName', payload.setName)
		if (payload.hasOwnProperty('len')) {
			set(state, 'questionsLength', payload.len)
		}

		for (let i = 0; i < payload.len; i++) {
			let id = payload.questionsIds[i]

			set(state.quiz_questions[id], 'selectedAnswer', null)
			set(state.quiz_questions[id], 'isResolved', false)
			set(state.quiz_questions[id], 'attemps', 0)
		}
	},
	[types.QUIZ_SELECT_ANSWER] (state, payload) {
		set(state.quiz_questions[payload.id], 'selectedAnswer', payload.answer)
	},
	[types.QUIZ_SHUFFLE_ANSWERS] (state, payload) {
		set(state.quiz_questions[payload.id], 'answers', _.shuffle(state.quiz_questions[payload.id].quiz_answers))
	},
	[types.QUIZ_TOGGLE_PROCESSING] (state, isProcessing) {
		set(state, 'processing', isProcessing)
	},
	[types.UPDATE_INCLUDED] (state, included) {
		_.each(included, (items, resource) => {
			_.each(items, (item, id) => {
				set(state[resource], id, item)
			})
		})
	},
	[types.QUIZ_DESTROY] (state) {
		let initialState = {
			attempts: [],
			comments: {},
			isComplete: false,
			loaded: false,
			questionsLength: 0,
			quiz_questions: {},
			quiz_answers: {},
			processing: false,
			profiles: {},
			setId: null,
			setName: '',
		}
		for (let field in state) {
			set(state, field, initialState[field])
		}
	},
}

const actions = {
	...commentsActions,
	setupQuestions({commit, dispatch, getters, state, rootGetters}, resource) {
		let storeKey = getLocalStorageKey(resource.id, rootGetters.currentUserSlug),
			storedState = store.get(storeKey)

		if (useLocalStorage() && !_.isUndefined(storedState)) {
			commit(types.QUIZ_RESTORE_STATE, storedState)
			commit(types.QUIZ_IS_LOADED)
			return true
		}

		fetchQuizSet(resource.id)
			.then((response) => {
				let included = response.data.included,
					questionsIds = response.data.quiz_questions,
					len = questionsIds.length

				commit(types.UPDATE_INCLUDED, included)

				commit(types.QUIZ_SET_QUESTIONS, {
					setId: response.data.id,
					setName: response.data.name,
					len: questionsIds.length,
					questionsIds,
				})
				commit(types.QUIZ_IS_LOADED)
				dispatch('saveQuiz')
			})
	},

	checkQuiz({state, commit, getters, dispatch}) {
		return new Promise((resolve, reject) => {
			commit(types.QUIZ_TOGGLE_PROCESSING, true)

			_.each(getters.getUnresolved, question => {
				let selectedId = question.quiz_answers[question.selectedAnswer],
					selected = state.quiz_answers[selectedId],
					id = question.id

				if (!_.isNull(selected) && selected.is_correct) {
					commit(types.QUIZ_RESOLVE_QUESTION, {id})
				} else {
					commit(types.QUIZ_RESET_ANSWER, {id})
					if (!question.preserve_order) {
						commit(types.QUIZ_SHUFFLE_ANSWERS, {id})
					}
				}
			})

			commit(types.QUIZ_ATTEMPT, { score: getters.getCurrentScore })

			if (getters.getUnresolved.length === 0) {
				commit(types.QUIZ_COMPLETE)
			}

			dispatch('saveQuiz')

			commit(types.QUIZ_TOGGLE_PROCESSING, false)
			resolve()
		})
	},

	saveQuiz({state, rootGetters}) {
		// TODO: Apr 24, 2017 - We must solve it better.
		let storeKey = getLocalStorageKey(state.setId, rootGetters.currentUserSlug)
		store.set(storeKey, state, new Date().getTime() + 3 * 60 * 60 * 1000)
	},

	destroyQuiz({commit}) {
		commit(types.QUIZ_DESTROY)
	},

	commitSelectAnswer({commit}, payload) {
		commit(types.QUIZ_SELECT_ANSWER, payload)
	},
}

export default {
	namespaced,
	state,
	getters,
	mutations,
	actions
}
