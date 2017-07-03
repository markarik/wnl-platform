import axios from 'axios'
import _ from 'lodash'
import {set, delete as destroy} from 'vue'
import {getApiUrl} from 'js/utils/env'
import {resource} from 'js/utils/config'
import {commentsGetters, commentsMutations, commentsActions} from 'js/store/modules/comments'
import {reactionsGetters, reactionsMutations, reactionsActions} from 'js/store/modules/reactions'
import * as types from 'js/store/mutations-types'
import quizStore, {getLocalStorageKey} from 'js/services/quizStore'

function fetchQuizSet(id) {
	return axios.get(
		getApiUrl(`quiz_sets/${id}?include=quiz_questions.quiz_answers,quiz_questions.comments.profiles,reactions`)
	)
}

function fetchQuizSetStats(id) {
	return axios.get(
		getApiUrl(`quiz_sets/${id}/stats`)
	)
}

function _fetchQuestionsCollection(ids) {
	return axios.post(getApiUrl('quiz_questions/.search'), {
		query: {
			whereIn: ['id', ids],
		},
		include: 'quiz_answers,comments.profiles,reactions',
	})
}

function getInitialState() {
	return {
		attempts: [],
		comments: {},
		isComplete: false,
		loaded: false,
		questionsIds: [],
		quiz_answers: {},
		quiz_questions: {},
		processing: false,
		profiles: {},
		setId: null,
		setName: '',
		quiz_stats: {},
		retry: false,
	}
}

// Should the module be namespaced?
const namespaced = true

// Initial state
const state = getInitialState()

const getters = {
	...commentsGetters,
	...reactionsGetters,
	getAnswers: (state) => (questionId) => {
		return state.quiz_questions[questionId].quiz_answers.map(
			(answerId) => state.quiz_answers[answerId]
		)
	},
	getAttempts: (state) => state.attempts,
	getComments: (state) => (id) => {
		return state.quiz_questions[id].comments.map((commentId) => state.comments[commentId])
	},
	getCurrentScore: (state, getters) => {
		return _.round(getters.getResolved.length * 100 / getters.questionsLength, 0)
	},
	getQuestions: (state) => state.questionsIds.map((id) => state.quiz_questions[id]),
	getResolved: (state, getters) => _.filter(getters.getQuestions, {'isResolved': true}),
	getSelectedAnswer: (state, getters) => (id) => state.quiz_questions[id].selectedAnswer,
	getUnresolved: (state, getters) => _.filter(getters.getQuestions, {'isResolved': false}),
	getUnanswered: (state, getters) => _.filter(
		getters.getQuestions, (question) => _.isNull(question.selectedAnswer)
	),
	isComplete: (state, getters) => state.isComplete || getters.getUnresolved.length === 0,
	isLoaded: (state) => state.loaded,
	isProcessing: (state) => state.processing,
	isResolved: (state) => (index) => state.quiz_questions[index].isResolved,
	getStats: (state) => (questionId) => state.quiz_stats[questionId],
	questionsLength: (state) => state.questionsIds.length
}

const mutations = {
	...commentsMutations,
	...reactionsMutations,
	[types.QUIZ_ATTEMPT] (state, payload) {
		state.attempts.push(payload)
	},
	[types.QUIZ_COMPLETE] (state) {
		set(state, 'isComplete', true)
	},
	[types.QUIZ_IS_LOADED] (state, loaded) {
		set(state, 'loaded', loaded)
	},
	[types.QUIZ_RESET_ANSWER] (state, payload) {
		set(state.quiz_questions[payload.id], 'selectedAnswer', null)
	},
	[types.QUIZ_RESOLVE_QUESTION] (state, payload) {
		set(state.quiz_questions[payload.id], 'isResolved', true)
	},
	[types.QUIZ_RESTORE_STATE] (state, payload) {
		set(state, 'setId', payload.setId)
		set(state, 'setName', payload.setName)
		set(state, 'attempts', payload.attempts)
		set(state, 'isComplete', payload.isComplete)
		set(state, 'questionsIds', payload.questionsIds)

		_.forEach(payload.quiz_questions, (value, id) => {
			if (!_.isUndefined(state.quiz_questions[id])) {
				if (!_.isUndefined(value.selectedAnswer)) {
					set(state.quiz_questions[id], 'selectedAnswer', value.selectedAnswer)
				}
				if (!_.isUndefined(value.isResolved)) {
					set(state.quiz_questions[id], 'isResolved', value.isResolved)
				}
			}
		})
	},
	[types.QUIZ_SET_QUESTIONS] (state, payload) {
		set(state, 'setId', payload.setId)
		set(state, 'setName', payload.setName)
		set(state, 'questionsIds', payload.questionsIds)

		for (let i = 0; i < payload.len; i++) {
			let id = payload.questionsIds[i]

			set(state.quiz_questions[id], 'selectedAnswer', null)
			set(state.quiz_questions[id], 'isResolved', false)
		}
	},
	[types.QUIZ_SELECT_ANSWER] (state, payload) {
		set(state.quiz_questions[payload.id], 'selectedAnswer', payload.answer)
	},
	[types.QUIZ_SHUFFLE_ANSWERS] (state, payload) {
		set(state.quiz_questions[payload.id], 'answers', _.shuffle(state.quiz_questions[payload.id].quiz_answers))
	},
	[types.QUIZ_TOGGLE_PROCESSING] (state, isProcessing) {
		set(state, 'processing', isProcessing)
	},
	[types.UPDATE_INCLUDED] (state, included) {
		_.each(included, (items, resource) => {
			let resourceObject = state[resource]
			_.each(items, (item, index) => {
				set(resourceObject, item.id, item)
			})
		})
	},
	[types.QUIZ_DESTROY] (state) {
		let initialState = getInitialState()
		Object.keys(initialState).forEach((field) => {
			set(state, field, initialState[field])
		})
	},
	[types.QUIZ_SET_STATS] (state, {stats}) {
		set(state, 'quiz_stats', stats)
	},
	[types.QUIZ_RESET_PROGRESS] (state) {
		Object.keys(state.quiz_questions).forEach((questionId) => {
			const updatedState = {
				...state.quiz_questions[questionId],
				isResolved: false,
				selectedAnswer: null,
			}

			set(state.quiz_questions, questionId, updatedState);
			set(state, 'isComplete', false);
			set(state, 'attempts', [])
			set(state, 'retry', true)
		})
	},
}

const actions = {
	...commentsActions,
	...reactionsActions,
	setupQuestions({commit, rootGetters}, resource) {
		commit(types.QUIZ_IS_LOADED, false)

		Promise.all([
			quizStore.getQuizProgress(resource.id, rootGetters.currentUserSlug),
			fetchQuizSet(resource.id),
			fetchQuizSetStats(resource.id)
		]).then(([storedState, response, quizStats]) => {
			let included = response.data.included,
				questionsIds = response.data.quiz_questions,
				len = questionsIds.length

			commit(types.UPDATE_INCLUDED, included)

			if (!_.isEmpty(storedState)) {
				commit(types.QUIZ_RESTORE_STATE, storedState)
			} else {
				commit(types.QUIZ_SET_QUESTIONS, {
					setId: response.data.id,
					setName: response.data.name,
					len,
					questionsIds,
				})
			}
			commit(types.QUIZ_TOGGLE_PROCESSING, false)
			commit(types.QUIZ_IS_LOADED, true)
			commit(types.QUIZ_SET_STATS, quizStats.data)
		});
	},

	fetchQuestionsCollection({commit}, ids) {
		_fetchQuestionsCollection(ids).then(response => {
			let included = _.clone(response.data.included)

			destroy(response.data, 'included')
			included['quiz_questions'] = response.data

			let questionsIds = _.map(response.data, (question) => question.id),
				len = questionsIds.length

			commit(types.UPDATE_INCLUDED, included)
			commit(types.QUIZ_SET_QUESTIONS, {
				setId: 0,
				setName: 'Kolekcja pytań kontrolnych',
				len,
				questionsIds,
			})
			commit(types.QUIZ_RESET_PROGRESS)
			commit(types.QUIZ_TOGGLE_PROCESSING, false)
			commit(types.QUIZ_IS_LOADED, true)
		})
	},

	checkQuiz({state, commit, getters, dispatch, rootGetters}) {
		return new Promise((resolve) => {
			commit(types.QUIZ_TOGGLE_PROCESSING, true)
			const data = [];
			const attempts = getters.getAttempts.length;

			_.each(getters.getUnresolved, question => {
				let selectedId = question.quiz_answers[question.selectedAnswer],
					selected = state.quiz_answers[selectedId],
					id = question.id

				if (attempts === 0) {
					data.push({
						'quiz_question_id': id,
						'quiz_answer_id': selectedId,
						'user_id': rootGetters.currentUserId
					});
				}

				if (!_.isNull(selected) && selected.is_correct) {
					commit(types.QUIZ_RESOLVE_QUESTION, {id})
				} else if (attempts < 2) {
					commit(types.QUIZ_RESET_ANSWER, {id})
					if (!question.preserve_order) {
						commit(types.QUIZ_SHUFFLE_ANSWERS, {id})
					}
				}
			})

			commit(types.QUIZ_ATTEMPT, {score: getters.getCurrentScore})

			dispatch('saveQuiz', data);

			if (getters.getUnresolved.length === 0) {
				commit(types.QUIZ_COMPLETE)
			}

			commit(types.QUIZ_TOGGLE_PROCESSING, false)
			resolve()
		})
	},

	saveQuiz({state, rootGetters}, recordedAnswers){
		quizStore.saveQuizProgress(state.setId, rootGetters.currentUserSlug, state, recordedAnswers);
	},

	autoResolve({state, commit}) {
		state.questionsIds.forEach(id => commit(types.QUIZ_RESOLVE_QUESTION, {id}))
		commit(types.QUIZ_COMPLETE)
	},

	resetState({state, commit}) {
		commit(types.QUIZ_RESET_PROGRESS)
	},

	destroyQuiz({commit}){
		return new Promise((resolve, reject) => {
			commit(types.QUIZ_IS_LOADED, false)
			commit(types.QUIZ_DESTROY)
			resolve()
		})
	},

	commitSelectAnswer({commit}, payload){
		commit(types.QUIZ_SELECT_ANSWER, payload)
	},
}

export default {
	namespaced,
	state,
	getters,
	mutations,
	actions
}
