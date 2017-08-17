import { set } from 'vue'
import * as types from '../mutations-types'
import {getApiUrl} from 'js/utils/env'
import axios from 'axios'
import {commentsGetters, commentsMutations, commentsActions} from 'js/store/modules/comments'


const namespaced = true

// Initial state
const state = {
	quiz_questions: {},
	comments: {},
	profiles: {},
	filters: {
		// TODO transaltions
		encounter: {
			resolved: {
				name: 'Rozwiązane'
			},
			notResolved: {
				name: 'Nierozwiązane'
			}
		},
		correctness: {
			correct: {
				name: 'Poprawnie rozwiązane'
			},
			incorrect: {
				name: 'Niepoprawnie rozwiązane'
			}
		}
	}
}

// Getters
const getters = {
	...commentsGetters,
	questions: state => state.quiz_questions,
	filters: state => state.filters
}

// Mutations
const mutations = {
	...commentsMutations,
	[types.QUESTIONS_SET] (state, payload) {
		// TODO endpoint could return data in shape: {id: data, ...}
		const serialized = {};
		payload.forEach(question => {
			serialized[question.id] = {
				...question
			}
		})
		set(state, 'quiz_questions', serialized)
	},
	[types.QUESTIONS_SET_QUESTION_DATA] (state, {id, included: {quiz_answers, ...included}, comments}) {
		set(state.quiz_questions[id], 'comments', comments)

		if (quiz_answers) {
			const answers = Object.values(quiz_answers).map((answer) => answer)
			set(state.quiz_questions[id], 'answers', answers)
		}

		_.each(included, (items, resource) => {
			let resourceObject = state[resource]
			_.each(items, (item, index) => {
				set(resourceObject, item.id, item)
			})
		})
	},
	[types.QUESTIONS_DYNAMIC_FILTERS_SET] (state, {chrono, subjects}) {
		// TODO enpoint could return serialied data.
		const serialized = {
			chrono: {},
			subjects: {}
		};
		chrono.forEach((tag) => {
			serialized.chrono[tag.id] = {
				name: tag.name
			}
		})

		subjects.forEach((tag) => {
			serialized.subjects[tag.id] = {
				name: tag.name
			}
		})
		set(state.filters, 'chrono', serialized.chrono)
		set(state.filters, 'subjects', serialized.subjects)
	},
	[types.QUESTIONS_SELECT_ANSWER] (state, payload) {
		set(state.quiz_questions[payload.id], 'selectedAnswer', payload.answer)
	},
	[types.QUESTIONS_RESOLVE_QUESTION] (state, questionId) {
		set(state.quiz_questions[questionId], 'isResolved', true)
	},
}

// Actions
const actions = {
	...commentsActions,
	fetchQuestions({commit}) {
		return _fetchAllQuestions()
			.then(({data}) => {
				commit(types.QUESTIONS_SET, data)
			})
	},
	fetchQuestionData({commit}, id) {
		return _fetchQuestionData(id)
			.then(({data}) => {
				commit(types.QUESTIONS_SET_QUESTION_DATA, data)
			})
	},
	fetchDynamicFilters({commit}) {
		return _fetchDynamicFilters()
			.then(({data}) => {
				commit(types.QUESTIONS_DYNAMIC_FILTERS_SET, data)
			})
	},
	selectAnswer({commit}, payload) {
		commit(types.QUESTIONS_SELECT_ANSWER, payload)
	},
	resolveQuestion({commit}, questionId) {
		commit(types.QUESTIONS_RESOLVE_QUESTION, questionId)
	},
}


const _fetchAllQuestions = () => {
	return axios.get(getApiUrl('questions'))
}

const _fetchQuestionData = (id) => {
	return axios.get(getApiUrl(`quiz_questions/${id}?include=quiz_answers,comments.profiles,reactions`))
}

const _fetchDynamicFilters = () => {
	return axios.get(getApiUrl('questions/filters'))
}

export default {
	namespaced,
	state,
	getters,
	mutations,
	actions
}
