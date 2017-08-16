import { set } from 'vue'
import * as types from '../mutations-types'
import {getApiUrl} from 'js/utils/env'
import axios from 'axios'

const namespaced = true

// Initial state
const state = {
	questions: {},
	filters: {
		// TODO transaltions
		encounter: {
			resolved: {
				selected: false,
				name: 'Rozwiązane'
			},
			notResolved: {
				selected: false,
				name: 'Nierozwiązane'
			}
		},
		correctness: {
			correct: {
				selected: false,
				name: 'Poprawnie rozwiązane'
			},
			incorrect: {
				selected: false,
				name: 'Niepoprawnie rozwiązane'
			}
		}
	}
}

// Getters
const getters = {
	questions: state => state.questions,
	questionsList: state => Object.values(state.questions),
	filters: state => state.filters
}

// Mutations
const mutations = {
	[types.QUESTIONS_SET] (state, payload) {
		// TODO endpoint could return data in shape: {id: data, ...}
		const serialized = {};
		payload.forEach(question => {
			serialized[question.id] = {
				...question
			}
		})
		set(state, 'questions', serialized)
	},
	[types.QUESTIONS_SET_ANSWER] (state, {id, included}) {
		if (included.quiz_answers) {
			const answers = Object.values(included.quiz_answers).map((answer) => answer)
			set(state.questions[id], 'answers', answers)
		}
	},
	[types.QUESTIONS_DYNAMIC_FILTERS_SET] (state, {chrono, subjects}) {
		const serialized = {
			chrono: {},
			subjects: {}
		};
		chrono.forEach((tag) => {
			serialized.chrono[tag.id] = {
				name: tag.name,
				selected: false
			}
		})

		subjects.forEach((tag) => {
			serialized.subjects[tag.id] = {
				name: tag.name,
				selected: false
			}
		})
		set(state.filters, 'chrono', serialized.chrono)
		set(state.filters, 'subjects', serialized.subjects)
	}
}

// Actions
const actions = {
	fetchQuestions({commit}) {
		return _fetchAllQuestions()
			.then(({data}) => {
				commit(types.QUESTIONS_SET, data)
			})
	},
	fetchQuestionAnswers({commit}, id) {
		return _fetchQuestionAnswers(id)
			.then(({data}) => {
				commit(types.QUESTIONS_SET_ANSWER, data)
			})
	},
	fetchDynamicFilters({commit}) {
		return _fetchDynamicFilters()
			.then(({data}) => {
				commit(types.QUESTIONS_DYNAMIC_FILTERS_SET, data)
			})
	}
}


const _fetchAllQuestions = () => {
	return axios.get(getApiUrl('questions'))
}

const _fetchQuestionAnswers = (id) => {
	return axios.get(getApiUrl(`quiz_questions/${id}?include=quiz_answers`))
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
