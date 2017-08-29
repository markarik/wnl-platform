import { set, delete as destroy } from 'vue'
import { get, isEqual, isEmpty, merge, size } from 'lodash'
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

const LIMIT = 25

// Initial state
const state = {
	activeFilters: [],
	comments: {},
	currentQuestion: {
		index: 0,
		page: 0,
	},
	filters: {
		// TODO Translations and move it to backend
		'planned': {
			type: FILTER_TYPES.LIST,
			items: [
				{
					name: 'Zaplanowane na dziś',
					value: new Date(),
				}
			],
		},
		'resolution': {
			type: FILTER_TYPES.LIST,
			items: [
				{
					name: 'Nierozwiązane',
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
	questionsPages: {
		// {pageNumber} => []
	},
	quiz_questions: {},
	profiles: {},
	results: false,
	testQuestions: [],
}

// Getters
const getters = {
	...commentsGetters,
	...reactionsGetters,
	activeFilters: state => state.activeFilters,
	activeFiltersNames: state => state.activeFilters.map(path => {
		return get(state.filters, path).name
	}),
	activeFiltersValues: state => state.activeFilters.map(path => {
		return get(state.filters, path).value
	}),
	allQuestionsCount: state => state.allCount,
	currentQuestion: state => {
		if (isEmpty(state.questionsPages) || !state.currentQuestion.page) return {}
		const {page, index} = state.currentQuestion

		return {page, index, ...state.quiz_questions[state.questionsPages[page][index]]}
	},
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
	getPage: state => page => state.questionsPages[page],
	matchedQuestionsCount: state => state.total,
	meta: state => ({
		lastPage: state.last_page,
		currentPage: state.current_page,
		perPage: state.per_page,
	}),
	questions: state => state.quiz_questions,
	questionsList: state => Object.values(state.quiz_questions || {}),
	questionsCurrentPage: state => {
		const ids = state.questionsPages[state.current_page]

		return isEmpty(ids) ? [] : ids.map(id => state.quiz_questions[id])
	},
	results: state => state.results,
	testQuestions: state => state.testQuestions.map(id => state.quiz_questions[id]),
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
	[types.QUESTIONS_DYNAMIC_FILTERS_SET] (state, {exams, subjects}) {
		const existingFilters = state.filters

		set(state, 'filters', {...existingFilters, exams, subjects})
	},
	[types.QUESTIONS_RESET_TEST] (state) {
		set(state, 'testQuestions', [])
	},
	[types.QUESTIONS_RESET_PAGES] (state) {
		set(state, 'questionsPages', {})
	},
	[types.QUESTIONS_RESOLVE_QUESTION] (state, questionId) {
		set(state.quiz_questions[questionId], 'isResolved', true)
	},
	[types.QUESTIONS_SELECT_ANSWER] (state, payload) {
		set(state.quiz_questions[payload.id], 'selectedAnswer', payload.answer)
	},
	[types.QUESTIONS_SET_CURRENT] (state, {page, index}) {
		set(state, 'currentQuestion', {page, index})
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
	[types.QUESTIONS_SET_PAGE] (state, page) {
		set(state, 'current_page', page)
	},
	[types.QUESTIONS_SET_TEST] (state, {questions, answers}) {
		let testQuestions = []

		questions.forEach(question => {
			testQuestions.push(question.id)

			set(state.quiz_questions, question.id, {
				...question,
				answers: question.quiz_answers.map(id => answers[id]),
				selectedAnswer: false,
				isResolved: false,
			})
		})

		set(state, 'testQuestions', testQuestions)
	},
	[types.QUESTIONS_SET_WITH_ANSWERS] (state, {questions, answers, page}) {
		const pageIds = []

		questions.forEach(question => {
			pageIds.push(question.id)

			set(state.quiz_questions, question.id, {
				...question,
				answers: question.quiz_answers.map(id => answers[id]),
				selectedAnswer: false,
				isResolved: false,
			})
		})

		set(state.questionsPages, page, pageIds)
	},
	[types.QUESTIONS_UPDATE] (state, {data: questions}) {
		questions.forEach(question => {
			const original = state.quiz_questions[question.id]
			set(state.quiz_questions, question.id, {...original, ...question})
		})
	},
	[types.UPDATE_INCLUDED] (state, included) {
		_.each(included, (items, resource) => {
			let resourceObject = state[resource]
			_.each(items, (item, index) => {
				set(resourceObject, item.id, item)
			})
		})
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
	buildPlan({state, getters, rootGetters, commit}, {activeFilters, startDate, endDate, slackDays}) {
		const filters = _parseFilters(activeFilters, state, getters, rootGetters);
		return axios.post(getApiUrl('user-plan/2'), {
			filters,
			startDate,
			endDate,
			slackDays
		})
	},
	changeCurrentQuestion({state, getters, commit}, {page, index}) {
		return new Promise((resolve, reject) => {
			commit(types.QUESTIONS_SET_CURRENT, {page, index})
			return resolve(getters.currentQuestion)
		})
	},
	checkQuestions({commit, getters, dispatch}) {
		const results = {
				unanswered: [],
				incorrect: [],
				correct: []
			},
			questionsToStore = []


		getters.testQuestions.forEach((question) => {
			if ([null, false].indexOf(question.selectedAnswer) > -1) {
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
	fetchDynamicFilters({commit}) {
		return _fetchDynamicFilters()
			.then(({data}) => {
				commit(types.QUESTIONS_DYNAMIC_FILTERS_SET, data)
			})
	},
	fetchQuestions({commit, state, getters, rootGetters}, {filters, page}) {
		const parsedFilters = _parseFilters(filters, state, getters, rootGetters)

		return _fetchQuestions({filters: parsedFilters, include: 'quiz_answers', page})
			.then(function(response) {
				const {answers, questions, meta, included} = _handleResponse(response, commit)

				commit(types.QUESTIONS_SET_WITH_ANSWERS, {
					answers,
					questions,
					page: meta.current_page,
				})
				commit(types.QUESTIONS_SET_META, meta)
				commit(types.UPDATE_INCLUDED, included)

				return response
			})
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
	fetchPage({state, commit, dispatch}, page) {
		return new Promise(resolve => {
			return dispatch('fetchQuestions', {filters: state.activeFilters, page})
				.then(response => resolve(response))
		})
	},
	fetchTestQuestions({commit, state, getters, rootGetters}, {activeFilters, count: limit}) {
		const filters = _parseFilters(activeFilters, state, getters, rootGetters)

		return _fetchQuestions({
			filters,
			limit,
			randomize: true,
			include: 'quiz_answers,reactions,comments.profiles'
		}).then(response => {
			const {answers, questions, included} = _handleResponse(response, commit)

			commit(types.QUESTIONS_SET_TEST, {answers, questions})
			commit(types.UPDATE_INCLUDED, included)

			return response
		})
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
	selectAnswer({commit}, payload) {
		commit(types.QUESTIONS_SELECT_ANSWER, payload)
	},
	setPage({commit}, page) {
		commit(types.QUESTIONS_SET_PAGE, page)
	},
	resolveQuestion({commit}, questionId) {
		commit(types.QUESTIONS_RESOLVE_QUESTION, questionId)
	},
	resetCurrentQuestion({commit}) {
		commit(types.QUESTIONS_SET_CURRENT, {index: 0, page: 1})
	},
	resetPages({commit}) {
		commit(types.QUESTIONS_RESET_PAGES)
	},
	resetTest({commit}) {
		commit(types.QUESTIONS_RESET_TEST)
	},
}


const _fetchQuestions = (requestParams) => {
	return axios.post(getApiUrl('quiz_questions/.filter'), requestParams)
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

const _handleResponse = (response) => {
	var {data: {data, ...meta}} = response,
		quizQuestions = {},
		quiz_answers = {},
		included = {}

	if (size(data) > 0) {
		// this var is here on purpose due to error in babel and problems with spread operator :(
		var {included: {quiz_answers, ...included}, ...quizQuestions} = data
	}

	return {
		answers: quiz_answers,
		included,
		meta,
		questions: Object.values(quizQuestions),
	}
}

export default {
	actions,
	getters,
	mutations,
	namespaced,
	state,
}
