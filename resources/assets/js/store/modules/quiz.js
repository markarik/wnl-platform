import axios from 'axios'
import store from 'store'
import _ from 'lodash'
import { set } from 'vue'
import { getApiUrl } from 'js/utils/env'
import { resource } from 'js/utils/config'
import * as types from 'js/store/mutations-types'

// Should the module be namespaced?
const namespaced = true

// Initial state
const state = {
	loaded: false,
	processing: false,
	isComplete: false,
	attempts: 0,
	questionsLength: 0,
	questions: [],
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
	isLoaded: (state) => state.loaded,
	isProcessing: (state) => state.processing,
	isComplete: (state) => state.isComplete,
	getQuestions: (state) => state.questions,
	getUnresolved: (state) => state.questions.filter((question) => !question.isResolved),
	getUnanswered: (state) => state.questions.filter((question) => _.isNull(question.selectedAnswer)),
	isResolved: (state) => (index) => state.questions[index].isResolved,
	getSelectedAnswer: (state) => (index) => state.questions[index].selectedAnswer,
}

const mutations = {
	[types.QUIZ_IS_LOADED] (state) {
		set(state, 'loaded', true)
	},
	[types.QUIZ_RESET_ANSWER] (state, payload) {
		set(state.questions[payload.index], 'selectedAnswer', null)
	},
	[types.QUIZ_RESOLVE_QUESTION] (state, payload) {
		set(state.questions[payload.index], 'isResolved', true)
	},
	[types.QUIZ_SET_QUESTIONS] (state, payload) {
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
	setupQuestions({commit}, resource) {
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

						question.answers[j] = answer
					}

					question.answers = _.shuffle(question.answers)
					question.index = i
					question.selectedAnswer = null
					question.isResolved = false
					question.attemps = 0

					questions.push(question)
				}

				commit(types.QUIZ_SET_QUESTIONS, {questions, len})
				commit(types.QUIZ_IS_LOADED)
			})
	},

	checkQuiz({state, commit, getters}) {
		commit(types.QUIZ_TOGGLE_PROCESSING)

		_.each(getters.getUnresolved, question => {
			let selected = question.selectedAnswer,
				index = question.index

			if (!_.isNull(selected) && question.answers[selected].is_correct) {
				$wnl.logger.debug(`Answer correct! Question: ${index}, Answer: ${selected}`)
				commit(types.QUIZ_RESOLVE_QUESTION, {index})
			} else {
				$wnl.logger.debug(`Wrong answer! Question: ${index}`)
				commit(types.QUIZ_RESET_ANSWER, {index})
				commit(types.QUIZ_SHUFFLE_ANSWERS, {index})
			}
		})

		commit(types.QUIZ_TOGGLE_PROCESSING)
	},
}

export default {
	namespaced,
	state,
	getters,
	mutations,
	actions
}
