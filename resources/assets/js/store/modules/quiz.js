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
	questions: {}
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
	getQuestions: (state) => state.questions,
	getUnresolved: (state) => state.questions.filter((question) => !question.isResolved),
	getUnanswered: (state) => state.questions.filter((question) => _.isEmpty(question.selectedAnswer)),
}

const mutations = {
	[types.QUIZ_SET_QUESTIONS] (state, payload) {
		set(state, questions, payload)
	},
	[types.QUIZ_SELECT_ANSWER] (state, payload) {
		set(state, questions[])
	},
}

const actions = {

}

export default {
	namespaced,
	state,
	getters,
	mutations,
	actions
}
