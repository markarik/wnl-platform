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
	qnaQuestionsIds: (state) => state.qna_questions,
	qnaAnswersIds: (state) => state.qna_answers,
	quizQuestions: (state) => state.quizQuestions,
	slides: (state) => state.slides,
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
		// state[payload.resource].push(payload.item)
	},
}

const actions = {
	fetchReactions({commit}) {
		_getReactions().then(response => {
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
	}
}

export default {
	namespaced,
	state,
	getters,
	mutations,
	actions,
}
