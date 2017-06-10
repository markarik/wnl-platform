import * as types from '../mutations-types'
import {getApiUrl} from 'js/utils/env'
import {set} from 'vue'

function _fetchPresentables(slideshowId) {
	let data = {
		query: {
			where: [
				['presentable_type', 'App\Models\Slideshow'],
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
				['commentable_type', 'slides'],
				['id', 'in', slidesIds],
			],
		},
		order: {
			created_at: 'desc',
			id: 'asc',
		},
	}

	return axios.post(getApiUrl('comments/.search', data))
}

function getInitialState() {
	return {
		comments: {},
		loading: true,
		presentables: {
			/**
			 * order_number: {
			 * 	id:
			 * 	functional: {Boolean},
			 * },
			 */
		},
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
		console.log(payload)
	},
	[types.SLIDESHOW_SET_SLIDES] (state, payload) {
		console.log(payload)
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
				})
				.catch((error) => $wnl.logger.error(error))
		})
	},
	setupComments({commit}, slidesIds) {
		return new Promise((resolve, reject) => {
			_fetchPresentables(slidesIds)
				.then((response) => {
					commit(types.SLIDESHOW_SET_PRESENTABLES, response)
				})
				.catch((error) => $wnl.logger.error(error))
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
