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
		'planned': {
			type: FILTER_TYPES.LIST,
			items: [
				{
					name: 'Zaplanowane na dziś',
					value: 'today',
				}
			],
		},
		'resolution': {
			type: FILTER_TYPES.LIST,
			items: [
				{
					name: 'Bez odpowiedzi',
					value: 'unresolved',
				},
				{
					name: 'Rozwiązane błędnie',
					value: 'incorrect',
				},
				{
					name: 'Rozwiązane poprawnie',
					value: 'correct',
				},
			],
		}
	},
	quiz_questions: {},
	profiles: {},
	results: false
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
	filters: state => {
		const order = ['planned', 'resolution', 'subjects', 'exams']

		let filters = {}
		order.forEach(group => {
			if (state.filters.hasOwnProperty(group)) {
				filters[group] = state.filters[group]
			}
		})

		return filters
	},
	getQuestion: state => questionId => state.quiz_questions[questionId],
	matchedQuestionsCount: state => state.total,
	questions: state => state.quiz_questions,
	questionsList: state => Object.values(state.quiz_questions || {}),
	results: state => state.results,
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
	[types.ACTIVE_FILTERS_SET] (state, filters) {
		set(state, 'activeFilters', filters)
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
	[types.QUESTIONS_SET_QUESTION_DATA] (state, {id, included, comments}) {
		if (_.size(included) === 0) return

		comments && set(state.quiz_questions[id], 'comments', comments)

		let {included: quiz_answers, ...resources} = included
		_.each(resources, (items, resource) => {
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
	[types.UPDATE_INCLUDED] (state, included) {
		_.each(included, (items, resource) => {
			let resourceObject = state[resource]
			_.each(items, (item, index) => {
				set(resourceObject, item.id, item)
			})
		})
	},
	[types.QUESTIONS_UPDATE] (state, {data: questions}) {
		const serialized = state.quiz_questions || {}

		questions.forEach(question => {
			serialized[question.id] = {
				...serialized[question.id],
				...question
			}
		})

		set(state, 'quiz_questions', serialized)
	},
}

// Actions
const actions = {
	...commentsActions,
	...reactionsActions,
	activeFiltersSet({commit}, filters) {
		commit(types.ACTIVE_FILTERS_SET, filters)
	},
	activeFiltersToggle({commit}, {filter, active}) {
		return new Promise(resolve => {
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
	fetchQuestionsCount({commit}) {
		return axios.get(getApiUrl('quiz_questions/.count'))
			.then(({data}) => {
				commit(types.QUESTIONS_SET_META, {allCount: data.count})
			})
	},
	fetchQuestionData({commit}, id) {
		return _fetchQuestionsComments(id)
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
	fetchQuestions({commit, state, getters, rootGetters}, {filters}) {
		const parsedFilters = _parseFilters(filters, state, getters, rootGetters)

		return _fetchQuestions({filters: parsedFilters, include: 'quiz_answers'})
			.then(response => _handleResponse(response, commit))
	},
	fetchTestQuestions({commit, state, getters, rootGetters}, {activeFilters, count: limit}) {
		const filters = _parseFilters(activeFilters, state, getters, rootGetters)

		return _fetchQuestions({
			filters,
			limit,
			randomize: true,
			include: 'quiz_answers,reactions,comments.profiles'
		}).then(response => _handleResponse(response, commit))
	},
	fetchQuestionsReactions({commit}, questionsIds) {
		return _fetchQuestions({
			filters: [
				{
					query: {
						whereIn: ['id', questionsIds],
					}
				}
			],
			include: 'reactions'
		}).then(({data}) => commit(types.QUESTIONS_UPDATE, data))
	},
	selectAnswer({commit}, payload) {
		commit(types.QUESTIONS_SELECT_ANSWER, payload)
	},
	resolveQuestion({commit}, questionId) {
		commit(types.QUESTIONS_RESOLVE_QUESTION, questionId)
	},
	checkQuestions({commit, getters, dispatch}) {
		const results = {
				unanswered: [],
				incorrect: [],
				correct: []
			},
			questionsToStore = []


		getters.questionsList.forEach((question) => {
			if (!question.selectedAnswer) {
				return results.unanswered.push(question)
			}
			const selectedAnswer = question.answers[question.selectedAnswer]

			selectedAnswer.is_correct ? results.correct.push(question) : results.incorrect.push(question)

			questionsToStore.push(question.id)
			dispatch('resolveQuestion', question.id)
		})

		dispatch('saveQuestionsResults', questionsToStore)

		// I'm not updating store on puropose - not sure if we want to keep results in VUEX store
		// if we decide to keep them here we need to remember about clearing them when exiting the "TEST MODE"
		// commit(types.QUESTIONS_SET_RESULTS, results)

		return Promise.resolve(results)
	},
	saveQuestionsResults({commit, getters, rootGetters}, questionIds) {
		const results = questionIds.map((questionId) => {
			const question = getters.getQuestion(questionId)

			if (!question.hasOwnProperty('selectedAnswer')) return
			if (!question.answers.hasOwnProperty(question.selectedAnswer)) return

			return {
				questionId,
				answerId: question.selectedAnswer
			}
		}).filter((result) => result)

		axios.post(getApiUrl(`quiz_results/${rootGetters.currentUserId}`), {results})
	},
	buildPlan({state, getters, rootGetters, commit}, {activeFilters, startDate, endDate, slackDays}) {
		const filters = _parseFilters(activeFilters, state, getters, rootGetters);
		return axios.post(getApiUrl('user-plan/2'), {
			filters,
			startDate,
			endDate,
			slackDays
		})
	}
}


const _fetchQuestions = (requestParams) => {
	// TODO pagination and other super stuff
	return axios.post(getApiUrl('quiz_questions/.filter'), {
		limit: 10,
		...requestParams
	})
}

const _fetchQuestionsComments = (id) => {
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
	var {data: {data, ...meta}} = response,
		quizQuestions = {},
		quiz_answers = {},
		included = {}

	if (size(data) > 0) {
		// this var is here on purpose due to error in babel and problems with spread operator :(
		var {included: {quiz_answers, ...included}, ...quizQuestions} = data
	}

	commit(types.QUESTIONS_SET_WITH_ANSWERS, {
		questions: Object.values(quizQuestions),
		answers: quiz_answers
	})
	commit(types.QUESTIONS_SET_META, meta)

	commit(types.UPDATE_INCLUDED, included)
}

export default {
	namespaced,
	state,
	getters,
	mutations,
	actions
}
