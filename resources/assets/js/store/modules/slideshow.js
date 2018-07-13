import _ from 'lodash'
import {set, delete as destroy} from 'vue'

import * as types from '../mutations-types'
import {getApiUrl} from 'js/utils/env'
import {modelToResourceMap} from 'js/utils/config'
import {commentsGetters, commentsMutations, commentsActions} from 'js/store/modules/comments'
import {reactionsGetters, reactionsActions, reactionsMutations} from 'js/store/modules/reactions'

function _fetchReactables(presentables) {
	let slideIds = presentables.map(presentable => presentable.slide_id)
	let data     = {
		query: {
			where: [
				['reactable_type', 'App\\Models\\Slide']
			],
			whereInMulti: [['reactable_id', slideIds], ['reaction_id', [4,5]]],
		},
	}

	console.time('wnl/action/slideshow/setup/setupPresentablesWithReactions/fetchReactables/request')
	return axios.post(getApiUrl('reactables/.search'), data)
		.then(response => {
			console.timeEnd('wnl/action/slideshow/setup/setupPresentablesWithReactions/fetchReactables/request')
			let bookmarked = {}
			let watched = {}
			response.data.forEach(reactable => {
				if (reactable.reaction_id === 4) bookmarked[reactable.reactable_id] = reactable
				if (reactable.reaction_id === 5) watched[reactable.reactable_id] = reactable
			})

			return presentables.map(presentable => {
				let slideId = presentable.slide_id
				presentable.bookmark = {
					hasReacted: bookmarked.hasOwnProperty(slideId)
				}

				presentable.watch = {
					hasReacted: watched.hasOwnProperty(slideId)
				}

				return presentable
			});
		})
}

function _fetchPresentables(slideshowId, type) {
	let data = {
		query: {
			where: [
				['presentable_type', type],
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

	console.time('wnl/action/slideshow/setup/setupPresentablesWithReactions/fetchPresentables/request')
	return axios.post(getApiUrl('presentables/.search'), data)
		.then((data) => {
			console.timeEnd('wnl/action/slideshow/setup/setupPresentablesWithReactions/fetchPresentables/request')
			return data
		})
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
		sortedSlidesIds: []
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
	isLoading: (state) => state.loading,
	slides: (state) => state.slides,
	getSlidePositionById: (state) => (slideId) => state.slides[slideId] ? state.slides[slideId].order_number : -1,
	slidesIds: (state) => Object.keys(state.slides),
	findRegularSlide: (state, getters) => (slideNumber, direction) => {
		let step   = direction === 'previous' ? -1 : 1,
			length = state.presentables.length

		for (; ; slideNumber = slideNumber + step) {
			if (!getters.isFunctional(slideNumber)) {
				return slideNumber
			}
			if (slideNumber <= 0) {
				return 1
			}
			if (slideNumber >= length) {
				return length
			}
		}
	},
	presentableSortedSlidesIds: state => {
		return state.presentables.map(presentable => presentable.slide_id)
	},
	slideshowSortedSlideIds: state => state.sortedSlidesIds,
	getSlideIdFromIndex: state => index => state.sortedSlidesIds[index],
	getSlideById: state => id => state.slides[id]
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
	[types.SLIDESHOW_SET_SLIDES] (state) {
		console.time(`wnl/slideshow/${types.SLIDESHOW_SET_SLIDES}`);
		const slides = {};
		state.presentables.forEach(presentable => {
			slides[presentable.slide_id] = {
				order_number: presentable.order_number,
				bookmark: presentable.bookmark,
				watch: presentable.watch,
				id: presentable.slide_id
			}
		});

		setSlidesV2(state, slides);
		console.timeEnd(`wnl/slideshow/${types.SLIDESHOW_SET_SLIDES}`);
	},
	[types.RESET_MODULE] (state) {
		let initialState = getInitialState()
		Object.keys(initialState).forEach((field) => {
			set(state, field, initialState[field])
		})
	},
	[types.SLIDESHOW_SET_SORTED_SLIDES_IDS] (state, ids) {
		set(state, 'sortedSlidesIds', ids)
	},
	[types.SLIDESHOW_SET_COMMENTS](state, comments) {
		console.time(`wnl/mutations/setComments/v2`)
		const slides = {...state.slides};

		const commentsBySlides = {};

		Object.values(comments).forEach((comment) => {
			if(commentsBySlides[comment.commentable_id]) {
				commentsBySlides[comment.commentable_id].push(comment.id)
			} else {
				commentsBySlides[comment.commentable_id] = [comment.id]
			}
		})

		Object.keys(commentsBySlides).forEach(slideId => {
			slides[slideId].comments = commentsBySlides[slideId]
		})

		set(state, 'slides', slides);
		console.timeEnd(`wnl/mutations/setComments/v2`)
	}
}

const actions = {
	...commentsActions,
	...reactionsActions,
	setup({commit, dispatch, getters}, {id, type='App\\Models\\Slideshow'}) {
		console.time('wnl/action/slideshow/setup');
		return new Promise((resolve, reject) => {
			dispatch('setupPresentablesWithReactions', {id, type})
				.then(() => {
					return dispatch('setupSlideshowCommentsV2', getters.slidesIds)
					// return dispatch('setupSlideshowComments', getters.slidesIds)
				})
				.then(() => {
					console.timeEnd('wnl/action/slideshow/setup');
					return resolve()
				})
				.catch((reason) => reject(reason))
		})
	},
	setupPresentablesWithReactions({commit}, {id, type='App\\Models\\Slideshow'}) {
		return new Promise((resolve, reject) => {
			_fetchPresentables(id, type)
				.then((response) => {
					return _fetchReactables(response.data)
				})
				.then((presentables) => {
					commit(types.SLIDESHOW_SET_PRESENTABLES, presentables)
					commit(types.SLIDESHOW_SET_SLIDES)
					return resolve()
				})
				.catch((error) => {
					$wnl.logger.error(error)
					reject()
				})
		})
	},
	setupPresentables({commit}, {id, type='App\\Models\\Slideshow'}) {
		return new Promise((resolve, reject) => {
			_fetchPresentables(id, type)
				.then((presentables) => {
					commit(types.SLIDESHOW_SET_PRESENTABLES, presentables)
					commit(types.SLIDESHOW_SET_SLIDES)
					resolve()
				})
				.catch((error) => {
					$wnl.logger.error(error)
					reject()
				})
		})
	},
	setupSlideshowComments({commit, dispatch}, slidesIds) {
		console.time('wnl/slideshow/setupSlideshowComments')
		return dispatch('setupComments', {ids: slidesIds, resource: modelToResourceMap['App\\Models\\Slide']})
			.then(() => {
				console.timeEnd('wnl/slideshow/setupSlideshowComments')
				return commit(types.IS_LOADING, false)
			})
			.catch(() => commit(types.IS_LOADING, false))
	},
	async setupSlideshowCommentsV2({commit, dispatch}, slidesIds) {
		console.time('wnl/slideshow/setupSlideshowComments/v2')
		try {
			const commentables = await dispatch('fetchComments', {ids: slidesIds, model: "App\\Models\\Slide"})
			commit(types.SLIDESHOW_SET_COMMENTS, commentables);
			commit(types.IS_LOADING, false)
		} catch (e) {
			commit(types.IS_LOADING, false)
		}
		console.timeEnd('wnl/slideshow/setupSlideshowComments/v2')
	},
	resetModule({commit}) {
		commit(types.RESET_MODULE)
	},
	setSortedSlidesIds({commit}, ids) {
		commit(types.SLIDESHOW_SET_SORTED_SLIDES_IDS, ids)
	}
}

const setSlidesV1 = (state) => {
	console.time('wnl/MUTATIONS/SET_SLIDES/v1')
	_.each(state.presentables, (element) => {
		set(state.slides, element.slide_id, {
			order_number: element.order_number,
			comments: [],
			bookmark: element.bookmark,
			watch: element.watch,
			id: element.slide_id
		})
	})
	console.timeEnd('wnl/MUTATIONS/SET_SLIDES/v1')
};

const setSlidesV2 = (state, slides) => {
	console.time('wnl/MUTATIONS/SET_SLIDES/v2')
	set(state, 'slides', slides)
	console.timeEnd('wnl/MUTATIONS/SET_SLIDES/v2')
}

export default {
	namespaced,
	state,
	getters,
	mutations,
	actions,
}
