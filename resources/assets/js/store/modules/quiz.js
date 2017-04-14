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
	isDone: false,
	attempts: 0,
	questions: []
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
	getQuestions: (state) => state.questions,
	getUnresolved: (state) => state.questions.filter((question) => !question.isResolved),
	getUnanswered: (state) => state.questions.filter((question) => _.isNull(question.selectedAnswer)),
}

const mutations = {
	[types.QUIZ_IS_LOADED] (state) {
		set(state, 'loaded', true)
	},
	[types.QUIZ_SET_QUESTIONS] (state, payload) {
		set(state, 'questions', payload)
	},
	[types.QUIZ_SELECT_ANSWER] (state, payload) {
		set(state, questions[payload.index].selectedAnswer, payload.answer)
	},
}

const actions = {
	setupQuestions({commit}, resource) {
		axios.get(getApiUrl(`${resource.name}/${resource.id}?include=questions.answers`))
			.then((response) => {
				const included = response.data.included
				const questionsIds = response.data.questions

				let questions = []

				for (let i = 0; i < questionsIds.length; i++) {
					let id = questionsIds[i],
						question = included.questions[id],
						answersIds = question.answers

					for (let j = 0; j < answersIds.length; j++) {
						let answerId = answersIds[j],
							answer = included.answers[answerId]

						question.answers[j] = answer
					}

					question.selectedAnswer = null
					question.isResolved = false
					question.attemps = 0

					questions.push(question)
				}

				commit(types.QUIZ_SET_QUESTIONS, questions)
				commit(types.QUIZ_IS_LOADED)
			})
	}
}

export default {
	namespaced,
	state,
	getters,
	mutations,
	actions
}
