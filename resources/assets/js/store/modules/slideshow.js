import _ from 'lodash'

import * as types from '../mutations-types'
import {getApiUrl} from 'js/utils/env'
import {set, delete as destroy} from 'vue'

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
	isFunctional: (state) => (slideNumber) => state.presentables[slideNumber].functional,
	isLoading:    (state) => state.isLoading,
	getId:        (state) => (slideNumber) => state.presentables[slideNumber].id,
	slidesIds:    (state) => Object.keys(state.slides),
}

const mutations = {
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
	setup({commit, dispatch, getters}, slideshowId) {
		dispatch('setupPresentables', slideshowId)
			.then(() => dispatch('setupComments', getters.slidesIds))
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
	setupComments({commit}, slidesIds) {
		return new Promise((resolve, reject) => {
			_fetchComments(slidesIds)
				.then((response) => {
					commit(types.SLIDESHOW_SET_COMMENTS, response.data)
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
