import { set } from 'vue'
import * as types from '../mutations-types'
import {getApiUrl} from 'js/utils/env'
import axios from 'axios'
import {commentsGetters, commentsMutations, commentsActions} from 'js/store/modules/comments'
import {reactionsGetters, reactionsMutations, reactionsActions} from 'js/store/modules/reactions'


const namespaced = true

const FILTER_TYPES = {
	TAG: 'TAG',
	BOOLEAN: 'BOOLEAN'

}

// Initial state
const state = {
	quiz_questions: {},
	comments: {},
	profiles: {},
	filters: {
		// TODO transaltions
		'correct_answer': {
			'correct': {
				name: 'Rozwiązane',
				value: true,
				field: 'correct',
				type: FILTER_TYPES.BOOLEAN,
			},
			'incorrect': {
				name: 'Nierozwiązane',
				field: 'correct',
				value: false,
				type: FILTER_TYPES.BOOLEAN,
			}
		},
		'is_done': {
			'is_done': {
				name: 'Poprawnie rozwiązane',
				field: 'is_done',
				value: true,
				type: FILTER_TYPES.BOOLEAN
			},
			'not_done': {
				name: 'Niepoprawnie rozwiązane',
				field: 'is_done',
				value: false,
				type: FILTER_TYPES.BOOLEAN
			}
		}
	}
}

// Getters
const getters = {
	...commentsGetters,
	...reactionsGetters,
	questions: state => state.quiz_questions,
	filters: state => state.filters
}

// Mutations
const mutations = {
	...commentsMutations,
	...reactionsMutations,
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
	[types.QUESTIONS_SET_META] (state, meta) {
		Object.keys(meta).forEach((key) => {
			set(state, key, meta[key])
		})
	},
	[types.QUESTIONS_SET_QUESTION_DATA] (state, {id, included: {quiz_answers, ...included}, comments}) {
		comments && set(state.quiz_questions[id], 'comments', comments)

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
				name: tag.name,
				value: tag.id,
				type: FILTER_TYPES.TAG
			}
		})

		subjects.forEach((tag) => {
			serialized.subjects[tag.id] = {
				name: tag.name,
				value: tag.id,
				type: FILTER_TYPES.TAG
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
	...reactionsActions,
	fetchQuestions({commit}) {
		return _fetchAllQuestions()
			.then(({data: {data, ...meta}}) => {
				commit(types.QUESTIONS_SET, data)
				commit(types.QUESTIONS_SET_META, meta)
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
	fetchMatchingQuestions({commit, state}, activeFilters) {
		const filters = [];
		const tags = [];

		activeFilters.forEach((filter) => {
			const selectedFilter = _.get(state.filters, filter)

			if (selectedFilter.type === FILTER_TYPES.TAG) {
				tags.push(selectedFilter.value)
			} else if (selectedFilter.type === FILTER_TYPES.BOOLEAN) {
				const [key] = filter.split('.')

				filters.push({
					[`quiz.${key}`]: {
						user_id: 2,
						[selectedFilter.field]: selectedFilter.value
					}
				})
			}
		})

		filters.push({
			tags
		})

		_fetchAllQuestions({filters})
			.then(({data: {data, ...meta}}) => {
				commit(types.QUESTIONS_SET, data)
				commit(types.QUESTIONS_SET_META, meta)
			})
	},
	selectAnswer({commit}, payload) {
		commit(types.QUESTIONS_SELECT_ANSWER, payload)
	},
	resolveQuestion({commit}, questionId) {
		commit(types.QUESTIONS_RESOLVE_QUESTION, questionId)
	},
}


const _fetchAllQuestions = (requestParams) => {
	// TODO pagination and other super stuff
	return axios.post(getApiUrl('quiz_questions/.filter'), {
		include: 'reactions',
		limit: 50,
		...requestParams
	})
}

const _fetchQuestionData = (id) => {
	return axios.get(getApiUrl(`quiz_questions/${id}?include=quiz_answers,comments.profiles`))
}

const _fetchDynamicFilters = () => {
	return axios.get(getApiUrl('quiz_questions/filters/get'))
}

export default {
	namespaced,
	state,
	getters,
	mutations,
	actions
}
