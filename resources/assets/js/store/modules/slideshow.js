import {set} from 'vue';

import * as types from 'js/store/mutations-types';
import {getApiUrl} from 'js/utils/env';
import {modelToResourceMap} from 'js/utils/config';
import {commentsGetters, commentsMutations, commentsActions, commentsState} from 'js/store/modules/comments';
import {reactionsGetters, reactionsActions, reactionsMutations} from 'js/store/modules/reactions';

function _fetchReactables(presentables) {
	let slideIds = presentables.map(presentable => presentable.slide_id);

	return axios.post(getApiUrl('reactables/current/savedSlides'), {
		slideIds
	})
		.then(response => {
			let bookmarked = {};
			let watched = {};
			response.data.forEach(reactable => {
				if (reactable.reaction_id === 4) bookmarked[reactable.reactable_id] = reactable;
				if (reactable.reaction_id === 5) watched[reactable.reactable_id] = reactable;
			});

			return presentables.map(presentable => {
				let slideId = presentable.slide_id;
				presentable.bookmark = {
					hasReacted: bookmarked.hasOwnProperty(slideId)
				};

				presentable.watch = {
					hasReacted: watched.hasOwnProperty(slideId)
				};

				return presentable;
			});
		});
}

function _fetchPresentables(slideshowId, type) {
	return axios.post(getApiUrl('presentables/slides'), {
		presentable_type: type,
		presentable_id: slideshowId
	});
}

function getInitialState() {
	return {
		...commentsState,
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
		sortedSlidesIds: [],
		loadingComments: true
	};
}

const namespaced = true;

const state = getInitialState();

const getters = {
	...commentsGetters,
	...reactionsGetters,
	isFunctional: (state) => (slideNumber) => {
		let slideIndex = slideNumber - 1;

		if (!state.presentables.hasOwnProperty(slideIndex)) return 0;

		return state.presentables[slideNumber - 1].is_functional;
	},
	isLoading: (state) => state.loading,
	isLoadingComments: state => state.loadingComments,
	slides: (state) => state.slides,
	getSlidePositionById: (state) => (slideId) => state.slides[slideId] ? state.slides[slideId].order_number : -1,
	slidesIds: (state) => Object.keys(state.slides),
	findRegularSlide: (state, getters) => (slideNumber, direction) => {
		let step   = direction === 'previous' ? -1 : 1,
			length = state.presentables.length;

		for (; ; slideNumber = slideNumber + step) {
			if (!getters.isFunctional(slideNumber)) {
				return slideNumber;
			}
			if (slideNumber <= 0) {
				return 1;
			}
			if (slideNumber >= length) {
				return length;
			}
		}
	},
	presentableSortedSlidesIds: state => {
		return state.presentables.map(presentable => presentable.slide_id);
	},
	slideshowSortedSlideIds: state => state.sortedSlidesIds,
	getSlideIdFromIndex: state => index => state.sortedSlidesIds[index],
	getSlideById: state => id => state.slides[id]
};

const mutations = {
	...commentsMutations,
	...reactionsMutations,
	[types.IS_LOADING] (state, isLoading) {
		set(state, 'loading', isLoading);
	},
	[types.SLIDESHOW_SET_PRESENTABLES] (state, payload) {
		set(state, 'presentables', payload);
	},
	[types.SLIDESHOW_SET_SLIDES] (state) {
		const slides = {};
		state.presentables.forEach(presentable => {
			slides[presentable.slide_id] = {
				order_number: presentable.order_number,
				bookmark: presentable.bookmark,
				watch: presentable.watch,
				id: presentable.slide_id
			};
		});

		set(state, 'slides', slides);
	},
	[types.RESET_MODULE] (state) {
		let initialState = getInitialState();
		Object.keys(initialState).forEach((field) => {
			set(state, field, initialState[field]);
		});
	},
	[types.SLIDESHOW_SET_SORTED_SLIDES_IDS] (state, ids) {
		set(state, 'sortedSlidesIds', ids);
	},
	[types.SLIDESHOW_LOADING_COMMENTS] (state, status) {
		set(state, 'loadingComments', status);
	}
};

const actions = {
	...commentsActions,
	...reactionsActions,
	setup({commit, dispatch, getters}, {id, type='App\\Models\\Slideshow'}) {
		return new Promise((resolve, reject) => {
			dispatch('setupPresentablesWithReactions', {id, type})
				.then(resolve)
				.catch((reason) => reject(reason));
		});
	},
	setupPresentablesWithReactions({commit}, {id, type='App\\Models\\Slideshow'}) {
		return new Promise((resolve, reject) => {
			_fetchPresentables(id, type)
				.then((response) => _fetchReactables(response.data))
				.then((presentables) => {
					commit(types.SLIDESHOW_SET_PRESENTABLES, presentables);
					commit(types.SLIDESHOW_SET_SLIDES);
					return resolve();
				})
				.catch((error) => {
					$wnl.logger.error(error);
					reject();
				});
		});
	},
	setupPresentables({commit}, {id, type='App\\Models\\Slideshow'}) {
		return new Promise((resolve, reject) => {
			_fetchPresentables(id, type)
				.then((presentables) => {
					commit(types.SLIDESHOW_SET_PRESENTABLES, presentables);
					commit(types.SLIDESHOW_SET_SLIDES);
					resolve();
				})
				.catch((error) => {
					$wnl.logger.error(error);
					reject();
				});
		});
	},
	setupSlideshowComments({commit, dispatch}, args) {
		commit(types.SLIDESHOW_LOADING_COMMENTS, true);
		return dispatch('setupComments', {resource: modelToResourceMap['App\\Models\\Slide'], ...args})
			.then(() => {
				commit(types.SLIDESHOW_LOADING_COMMENTS, false);
			})
			.catch(() => commit(types.SLIDESHOW_LOADING_COMMENTS, false));
	},
	setupSlideComments({commit, dispatch}, {id, ...args}) {
		return dispatch('setupSlideshowComments', {commentable_id: id, ...args});
	},
	resetModule({commit}) {
		commit(types.RESET_MODULE);
	},
	setSortedSlidesIds({commit}, ids) {
		commit(types.SLIDESHOW_SET_SORTED_SLIDES_IDS, ids);
	}
};

export default {
	namespaced,
	state,
	getters,
	mutations,
	actions,
};
