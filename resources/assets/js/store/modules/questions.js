import { set, delete as destroy } from 'vue'
import { get, size } from 'lodash'
import * as types from '../mutations-types'
import {getApiUrl} from 'js/utils/env'
import axios from 'axios'
import {commentsGetters, commentsMutations, commentsActions} from 'js/store/modules/comments'
import {reactionsGetters, reactionsMutations, reactionsActions} from 'js/store/modules/reactions'


const namespaced = true

const FILTER_TYPES = {
	BOOLEAN: 'boolean',
	LIST: 'list',
	TAGS: 'tags',
}

// Initial state
const state = {
	activeFilters: [],
	comments: {},
	filters: {
		// TODO Translations
		'resolution': {
			type: FILTER_TYPES.LIST,
			items: [
				{
					name: 'Nierozwiązane',
					value: 'unresolved',
				},
				{
					name: 'Poprawne',
					value: 'correct',
				},
				{
					name: 'Niepoprawne',
					value: 'incorrect',
				},
			],
		}
	},
	quiz_questions: {},
	profiles: {},
}

// Getters
const getters = {
	...commentsGetters,
	...reactionsGetters,
	activeFilters: state => state.activeFilters,
	activeFiltersValues: state => state.activeFilters.map(path => {
		return get(state.filters, path).value
	}),
	allQuestionsCount: state => state.allCount,
	questions: state => state.quiz_questions,
	filters: state => {
		const order = ['resolution', 'subjects', 'exams']

		let filters = {}
		order.forEach(group => {
			if (state.filters.hasOwnProperty(group)) {
				filters[group] = state.filters[group]
			}
		})

		return filters
	},
	matchedQuestionsCount: state => state.total,
}

// Mutations
const mutations = {
	...commentsMutations,
	...reactionsMutations,
	[types.ACTIVE_FILTERS_ADD] (state, filter) {
		if (state.activeFilters.indexOf(filter) === -1) {
			state.activeFilters.push(filter)
		}
	},
	[types.ACTIVE_FILTERS_REMOVE] (state, filter) {
		const index = state.activeFilters.indexOf(filter)
		if (index > -1) {
			state.activeFilters.splice(index, 1)
		}
	},
	[types.ACTIVE_FILTERS_RESET] (state, payload) {
		state.activeFilters = []
	},
	[types.QUESTIONS_SET_WITH_ANSWERS] (state, {questions, answers}) {
		const serialized = {}

		questions.forEach(question => {
			serialized[question.id] = {
				...question,
				answers: _.pick(answers, question.quiz_answers)
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

		_.each(included, (items, resource) => {
			let resourceObject = state[resource]
			_.each(items, (item, index) => {
				set(resourceObject, item.id, item)
			})
		})
	},
	[types.QUESTIONS_DYNAMIC_FILTERS_SET] (state, {exams, subjects}) {
		const existingFilters = state.filters

		set(state, 'filters', {...existingFilters, exams, subjects})
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
	activeFiltersToggle({commit}, {filter, active}) {
		return new Promise((resolve, reject) => {
			if (active) {
				commit(types.ACTIVE_FILTERS_ADD, filter)
			} else {
				commit(types.ACTIVE_FILTERS_REMOVE, filter)
			}
			resolve()
		})
	},
	activeFiltersReset({commit}) {
		commit(types.ACTIVE_FILTERS_RESET)
	},
	fetchAllQuestions({commit}) {
		return _fetchQuestions({
			include: 'reactions,quiz_answers'
		}).then(response => _handleResponse(response, commit))
	},
	fetchQuestionsCount({commit}) {
		return axios.get(getApiUrl('quiz_questions/.count'))
			.then(({data}) => {
				commit(types.QUESTIONS_SET_META, {allCount: data.count})
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
	fetchMatchingQuestions({commit, state, getters, rootGetters}, activeFilters) {
		const filters = _parseFilters(activeFilters, state, getters, rootGetters)

		return _fetchQuestions({filters, include: 'quiz_answers,reactions'})
			.then(response => _handleResponse(response, commit))
	},
	fetchTestQuestions({commit, state, getters, rootGetters}, {activeFilters, count: limit}) {
		const filters = _parseFilters(activeFilters, state, getters, rootGetters)

		return _fetchQuestions({
			filters,
			limit,
			randomize: true,
			include: 'quiz_answers'
		}).then(response => _handleResponse(response, commit))
	},
	selectAnswer({commit}, payload) {
		commit(types.QUESTIONS_SELECT_ANSWER, payload)
	},
	resolveQuestion({commit}, questionId) {
		commit(types.QUESTIONS_RESOLVE_QUESTION, questionId)
	},
}


const _fetchQuestions = (requestParams) => {
	// TODO pagination and other super stuff
	return axios.post(getApiUrl('quiz_questions/.filter'), {
		limit: 50,
		...requestParams
	})
}

const _fetchQuestionData = (id) => {
	return axios.get(getApiUrl(`quiz_questions/${id}?include=comments.profiles`))
}

const _fetchDynamicFilters = () => {
	return axios.get(getApiUrl('quiz_questions/filters/get'))
}

const _parseFilters = (activeFilters, state, getters, rootGetters) => {
	const filters = []
	const groupedFilters = {}

	activeFilters.forEach((filter, index) => {
		const [filterGroup, ...tail] = filter.split('.')
		const filterValue = getters.activeFiltersValues[index]
		const filterType = state.filters[filterGroup].type

		groupedFilters[filterGroup] = groupedFilters[filterGroup] || []
		groupedFilters[filterGroup].push(filterValue)
	})

	Object.keys(groupedFilters).forEach((group) => {
		if (state.filters[group].type === FILTER_TYPES.TAGS) {
			filters.push({tags: groupedFilters[group]})
		} else if (state.filters[group].type === FILTER_TYPES.LIST) {
			filters.push({
				[`quiz.${group}`]: {
					user_id: rootGetters.currentUserId,
					list: groupedFilters[group]
				}
			})
		}
	})

	return filters;
}

const _handleResponse = (response, commit) => {
	let {data: {data, ...meta}} = response, quizQuestions = {}, quiz_answers = {}

	if (size(data) > 0) {
		({included: {quiz_answers}, ...quizQuestions} = data)
	}

	commit(types.QUESTIONS_SET_WITH_ANSWERS, {
		questions: Object.values(quizQuestions),
		answers: quiz_answers
	})
	commit(types.QUESTIONS_SET_META, meta)
}

export default {
	namespaced,
	state,
	getters,
	mutations,
	actions
}
