import _ from 'lodash'
import {set, delete as destroy} from 'vue'

import * as types from '../mutations-types'
import {getApiUrl} from 'js/utils/env'
import {commentsGetters, commentsMutations, commentsActions} from 'js/store/modules/comments'
import {reactionsGetters, reactionsActions, reactionsMutations} from 'js/store/modules/reactions'

function _fetchPresentables(slideshowId) {
	let data = {
		query: {
			where: [
				['presentable_type', 'App\\Models\\Slideshow'],
				['presentable_id', '=', slideshowId],
			],
		},
		join: [
			['slides', 'presentables.slide_id', '=', 'slides.id'],
		],
		order: {
			order_number: 'asc',
		},
		include: 'reactions',

	}

	return axios.post(getApiUrl('presentables/.search'), data)
}

function _fetchPresentablesByPresentable({type, id}) {
	let data = {
		query: {
			where: [
				['presentable_type', type],
				['presentable_id', '=', id],
			],
		},
		join: [
			['slides', 'presentables.slide_id', '=', 'slides.id'],
		],
		order: {
			order_number: 'asc',
		},
		include: 'reactions',

	}

	return axios.post(getApiUrl('presentables/.search'), data)
}

function _fetchComments(slidesIds) {
	let data = {
		query: {
			where: [
				['commentable_type', 'App\\Models\\Slide'],
			],
			whereIn: ['commentable_id', slidesIds],
		},
		order: {
			id: 'asc',
		},
		include: 'profiles',
	}

	return axios.post(getApiUrl('comments/.search'), data)
}

function getInitialState() {
	return {
		comments: {},
		loading: true,
		presentables: [
			/**
			 * {
			 * 	id: {Integer},
			 * 	is_functional: {Boolean},
			 * 	order_number: {Integer},
			 * 	presentable_id: {Integer},
			 * 	presentable_type: {String},
			 * 	slide_id: {Integer},
			 * },
			 * ...
			 */
		],
		profiles: {},
		slides: {
			/**
			 * id: {
			 * 	id: {Integer},
			 * 	comments: {Array},
			 * }
			 */
		},
	}
}

const namespaced = true

const state = getInitialState()

const getters = {
	...commentsGetters,
	...reactionsGetters,
	isFunctional: (state) => (slideNumber) => {
		let slideIndex = slideNumber - 1

		if (!state.presentables.hasOwnProperty(slideIndex)) return 0;

		return state.presentables[slideNumber - 1].is_functional
	},
	isLoading:    (state) => state.loading,
	getSlideId:   (state) => (slideOrderNumber) => {
		return state.presentables.length === 0 ? 0 : state.presentables[slideOrderNumber].id
	},
	slides: (state) => state.slides,
	getSlidePositionById: (state) => (slideId) => state.slides[slideId] ? state.slides[slideId].order_number : -1,
	slidesIds:    (state) => Object.keys(state.slides),
	bookmarkedSlideNumbers: (state) => {
		const slides = state.slides;

		return Object.keys(slides)
			.filter((slideIndex) => {
				return slides[slideIndex].bookmark.hasReacted === true
			})
			.map((slideIndex) => {
				return slides[slideIndex].order_number
			})
	},
	findRegularSlide: (state, getters) => (slideNumber, direction) => {
		let step = direction === 'previous' ? -1 : 1,
			length = state.presentables.length

		for (;;slideNumber = slideNumber + step) {
			if (!getters.isFunctional(slideNumber)) {
				return slideNumber
			}
			if (slideNumber <= 0 ) {
				return 1
			}
			if (slideNumber >= length) {
				return length
			}
		}
	}
}

const mutations = {
	...commentsMutations,
	...reactionsMutations,
	[types.IS_LOADING] (state, isLoading) {
		set(state, 'loading', isLoading)
	},
	[types.SLIDESHOW_SET_PRESENTABLES] (state, payload) {
		set(state, 'presentables', payload)
	},
	[types.SLIDESHOW_SET_SLIDES] (state, payload) {
		_.each(state.presentables, (element, index) => {
			set(state.slides, element.slide_id, {
				order_number: element.order_number,
				comments: [],
				bookmark: element.bookmark,
			})
		})
	},
	[types.SLIDESHOW_SET_COMMENTS] (state, payload) {
		set(state, 'profiles', payload.included.profiles)
		destroy(payload, 'included')

		_.each(payload, (comment, index) => {
			set(state.comments, comment.id, comment)
			state.slides[comment.commentable_id].comments.push(comment.id)
		})
	},
	[types.RESET_MODULE] (state) {
		let initialState = getInitialState()
		Object.keys(initialState).forEach((field) => {
			set(state, field, initialState[field])
		})
	},
}

const actions = {
	...commentsActions,
	...reactionsActions,
	setup({commit, dispatch, getters}, slideshowId) {
		return new Promise((resolve, reject) => {
			dispatch('setupPresentables', slideshowId)
				.then(() => dispatch('setupComments', getters.slidesIds))
				.then(() => resolve())
				.catch((reason) => reject(reason))
		})
	},
	setupByPresentable({commit, dispatch, getters}, presentable) {
		return new Promise((resolve, reject) => {
			dispatch('setupPresentablesByPresentable', presentable)
				.then(() => dispatch('setupComments', getters.slidesIds))
				.then(() => resolve())
				.catch((reason) => reject(reason))
		})
	},
	setupPresentables({commit}, slideshowId) {
		return new Promise((resolve, reject) => {
			_fetchPresentables(slideshowId)
				.then((response) => {
					commit(types.SLIDESHOW_SET_PRESENTABLES, response.data)
					commit(types.SLIDESHOW_SET_SLIDES)
					resolve()
				})
				.catch((error) => {
					$wnl.logger.error(error)
					reject()
				})
		})
	},
	setupPresentablesByPresentable({commit}, presentable) {
		return new Promise((resolve, reject) => {
			_fetchPresentablesByPresentable(presentable)
				.then((response) => {
					commit(types.SLIDESHOW_SET_PRESENTABLES, response.data)
					commit(types.SLIDESHOW_SET_SLIDES)
					resolve()
				})
				.catch((error) => {
					$wnl.logger.error(error)
					reject()
				})
		})
	},
	setupComments({commit}, slidesIds) {
		return new Promise((resolve, reject) => {
			_fetchComments(slidesIds)
				.then((response) => {
					if (!response.data.hasOwnProperty('included')) {
						commit(types.IS_LOADING, false)
						return resolve()
					}

					commit(types.SLIDESHOW_SET_COMMENTS, response.data)
					commit(types.IS_LOADING, false)
					resolve()
				})
				.catch((error) => {
					$wnl.logger.error(error)
					reject()
				})
		})
	},
}

export default {
	namespaced,
	state,
	getters,
	mutations,
	actions,
}
