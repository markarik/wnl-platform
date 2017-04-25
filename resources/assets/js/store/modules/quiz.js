import axios from 'axios'
import store from 'store'
import _ from 'lodash'
import { set } from 'vue'
import { getApiUrl } from 'js/utils/env'
import { resource } from 'js/utils/config'
import * as types from 'js/store/mutations-types'

function getLocalStorageKey(setId, userSlug) {
	return `wnl-quiz-${setId}-u-${userSlug}`
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
	isComplete: false,
	loaded: false,
	questionsLength: 0,
	questions: [],
	processing: false,
	setId: null,
	setName: '',
}

/*
question: {
	text: string,
	answers: array,
	isResolved: bool,
	selectedAnswer: int (original index of answer),
	attempts: int,
}
*/

const getters = {
	getAttempts: (state) => state.attempts,
	getCurrentScore: (state, getters) => {
		return _.round(getters.getResolved.length * 100 / state.questionsLength, 0)
	},
	getQuestions: (state) => state.questions,
	getResolved: (state) => state.questions.filter((question) => question.isResolved),
	getSelectedAnswer: (state) => (index) => state.questions[index].selectedAnswer,
	getUnresolved: (state) => state.questions.filter((question) => !question.isResolved),
	getUnanswered: (state) => state.questions.filter((question) => _.isNull(question.selectedAnswer)),
	isComplete: (state) => state.isComplete,
	isLoaded: (state) => state.loaded,
	isProcessing: (state) => state.processing,
	isResolved: (state) => (index) => state.questions[index].isResolved,
}

const mutations = {
	[types.QUIZ_ATTEMPT] (state, payload) {
		// set(state, 'attempts', state.attempts + 1)
		state.attempts.push(payload)
	},
	[types.QUIZ_COMPLETE] (state) {
		set(state, 'isComplete', true)
	},
	[types.QUIZ_IS_LOADED] (state) {
		set(state, 'loaded', true)
	},
	[types.QUIZ_RESET_ANSWER] (state, payload) {
		set(state.questions[payload.index], 'selectedAnswer', null)
	},
	[types.QUIZ_RESOLVE_QUESTION] (state, payload) {
		set(state.questions[payload.index], 'isResolved', true)
	},
	[types.QUIZ_RESTORE_STATE] (state, payload) {
		_.forEach(payload, (value, key) => {
			set(state, key, value)
		})
	},
	[types.QUIZ_SET_QUESTIONS] (state, payload) {
		set(state, 'setId', payload.setId)
		set(state, 'setName', payload.setName)
		set(state, 'questions', payload.questions)
		if (payload.hasOwnProperty('len')) {
			set(state, 'questionsLength', payload.len)
		}
	},
	[types.QUIZ_SELECT_ANSWER] (state, payload) {
		set(state.questions[payload.index], 'selectedAnswer', payload.answer)
	},
	[types.QUIZ_SHUFFLE_ANSWERS] (state, payload) {
		set(state.questions[payload.index], 'answers', _.shuffle(state.questions[payload.index].answers))
	},
	[types.QUIZ_TOGGLE_PROCESSING] (state) {
		set(state, 'processing', !state.processing)
	},
}

const actions = {
	setupQuestions({commit, dispatch, getters, state, rootGetters}, resource) {
		if (state.questionsLength > 0) {
			return false
		}

		let storeKey = getLocalStorageKey(resource.id, rootGetters.currentUserSlug),
			storedState = store.get(storeKey)
		if (!_.isUndefined(storedState)) {
			commit(types.QUIZ_RESTORE_STATE, storedState)
			commit(types.QUIZ_IS_LOADED)
			return true
		}

		axios.get(getApiUrl(`${resource.name}/${resource.id}?include=questions.answers`))
			.then((response) => {
				const included = response.data.included
				const questionsIds = response.data.questions
				const len = questionsIds.length

				let questions = []

				for (let i = 0; i < len; i++) {
					let id = questionsIds[i],
						question = included.questions[id],
						answersIds = question.answers

					for (let j = 0; j < answersIds.length; j++) {
						let answerId = answersIds[j],
							answer = included.answers[answerId]

						answer.stats = getPercentageShare(answer.hits, question.total_hits)

						question.answers[j] = answer
					}

					if (!question.preserve_order) {
						question.answers = _.shuffle(question.answers)
					}
					question.index = i
					question.selectedAnswer = null
					question.isResolved = false
					question.attemps = 0

					questions.push(question)
				}

				commit(types.QUIZ_SET_QUESTIONS, {
					setId: response.data.id,
					len,
					setName: response.data.name,
					questions,
				})
				commit(types.QUIZ_IS_LOADED)
				dispatch('saveQuiz')
			})
	},

	checkQuiz({commit, getters, dispatch}) {
		return new Promise((resolve, reject) => {
			commit(types.QUIZ_TOGGLE_PROCESSING)

			_.each(getters.getUnresolved, question => {
				let selected = question.selectedAnswer,
					index = question.index

				if (!_.isNull(selected) && question.answers[selected].is_correct) {
					commit(types.QUIZ_RESOLVE_QUESTION, {index})
				} else {
					commit(types.QUIZ_RESET_ANSWER, {index})
					if (!question.preserve_order) {
						commit(types.QUIZ_SHUFFLE_ANSWERS, {index})
					}
				}
			})

			commit(types.QUIZ_ATTEMPT, { score: getters.getCurrentScore })

			if (getters.getUnresolved.length === 0) {
				commit(types.QUIZ_COMPLETE)
			}

			commit(types.QUIZ_TOGGLE_PROCESSING)

			dispatch('saveQuiz')
			resolve()
		})
	},

	saveQuiz({state, rootGetters}) {
		// TODO: Apr 24, 2017 - We must solve it better.
		let storeKey = getLocalStorageKey(state.setId, rootGetters.currentUserSlug)
		store.set(storeKey, state, new Date().getTime() + 3 * 60 * 60 * 1000)
	},
}

export default {
	namespaced,
	state,
	getters,
	mutations,
	actions
}
