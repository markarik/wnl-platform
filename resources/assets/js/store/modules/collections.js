import _ from 'lodash'
import * as types from '../mutations-types'
import {getApiUrl} from 'js/utils/env'
import {set} from 'vue'

function _getReactions() {
	return axios.get(getApiUrl('users/current/reactions/bookmark'))
}

function getInitialState() {
	return {
		loading: true,
		qna_questions: [],
		qna_answers: [],
		quiz_questions: [],
		slides: [],
		categories: []
	}
}

const resourcesMap = {
	qna_answers: 'App\\Models\\QnaAnswer',
	qna_questions: 'App\\Models\\QnaQuestion',
	quiz_questions: 'App\\Models\\QuizQuestion',
	slides: 'App\\Models\\Slide',
}

const namespaced = true

const state = getInitialState()

const getters = {
	isLoading: (state) => state.loading,
	qnaQuestionsIds: (state) => state.qna_questions.map(question => question.reactable_id),
	qnaAnswersIds: (state) => state.qna_answers,
	quizQuestionsIds: (state) => state.quiz_questions.map(question => question.reactable_id),
	slides: (state) => state.slides,
	categories: (state) => state.categories
}

const mutations = {
	[types.IS_LOADING] (state, isLoading) {
		set(state, 'loading', isLoading)
	},
	[types.RESET_MODULE] (state) {
		let initialState = getInitialState()
		Object.keys(initialState).forEach((field) => {
			set(state, field, initialState[field])
		})
	},
	[types.COLLECTIONS_SET_REACTABLE] (state, payload) {
		payload.items.forEach((item) => state[payload.resource].push(item))
	},
	[types.COLLECTIONS_SET_CATEGORIES] (state, categories) {
		set(state, 'categories', categories)
	}
}

const actions = {
	fetchReactions({commit}) {
		return _getReactions().then(response => {
			if (response.data.length === 0) {
				commit(types.IS_LOADING, false)
				return false
			}

			_.each(resourcesMap, (model, resource) => {commit(types.COLLECTIONS_SET_REACTABLE, {
					resource,
					items: response.data.filter((item) => item.reactable_type === model)
				})
			})
			commit(types.IS_LOADING, false)
		})
	},
	fetchCategories({commit}) {
		return axios.get(getApiUrl('categories/all'))
			.then(({data: categories}) => commit(types.COLLECTIONS_SET_CATEGORIES, categories));
	}
}

export default {
	namespaced,
	state,
	getters,
	mutations,
	actions,
}
