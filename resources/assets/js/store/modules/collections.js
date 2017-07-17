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
		categories: [],
		slidesContent: []
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
	slidesIds: (state) => state.slides.map(slide => slide.reactable_id),
	categories: (state) => state.categories,
	slidesContent: (state) => state.slidesContent,
	getCategoryByName: (state, getters) => (categoryName) => getters.categories.find((category) => {
		return false
	})
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
	},
	[types.SLIDES_LOADING] (state, isLoading) {
		set(state, 'slidesLoaded', !isLoading)
	},
	[types.COLLECTIONS_SET_SLIDES] (state, slides) {
		set(state, 'slidesContent', slides)
	},
	[types.COLLECTIONS_APPEND_SLIDE] (state, slide) {
		const slidesContent = state.slidesContent || []

		set(state, 'slidesContent', [...slidesContent, slide])
	},
	[types.COLLECTIONS_REMOVE_SLIDE] (state, slideId) {
		const updatedSlides = state.slidesContent.filter(({id}) => slideId !== id)

		set(state, 'slidesContent', updatedSlides)
	}
}

const actions = {
	fetchReactions({commit}) {
		return _getReactions().then(({data: { reactions }}) => {
			if (reactions === 0) {
				commit(types.IS_LOADING, false)
				return false
			}

			_.each(resourcesMap, (model, resource) => {commit(types.COLLECTIONS_SET_REACTABLE, {
					resource,
					items: reactions.filter((item) => item.reactable_type === model)
				})
			})
			commit(types.IS_LOADING, false)
		})
	},
	fetchCategories({commit}) {
		return axios.get(getApiUrl('categories/all'))
			.then(({data: categories}) => commit(types.COLLECTIONS_SET_CATEGORIES, categories));
	},
	fetchSlidesByTagName({commit}, {tagName, ids}) {
		commit(types.SLIDES_LOADING, true);
		return axios.post(getApiUrl('slides/.search'), {
			query: {
				whereHas: {
					tags: {
						where: [['tags.name', '=', tagName]]
					}
				},
				whereIn: ['id', ids],
			},
			order: {
				id: 'desc',
			},
		}).then(({data}) => {
			commit(types.COLLECTIONS_SET_SLIDES, data)
			commit(types.SLIDES_LOADING, false);
		}).catch((error) => {
			commit(types.SLIDES_LOADING, false);
		})
	},
	addSlideToCollection({commit}, slideId) {
		return axios.post(getApiUrl('slides/.search'), {
			query: {
				where: [['id', '=', slideId]],
			},
			order: {
				id: 'desc',
			},
		}).then(({data}) => {
			data && data.length && commit(types.COLLECTIONS_APPEND_SLIDE, data[0]);
		})
	},
	removeSlideFromCollection({commit, state}, slideId) {
		commit(types.COLLECTIONS_REMOVE_SLIDE, slideId)
	}
}

export default {
	namespaced,
	state,
	getters,
	mutations,
	actions,
}
