import _ from 'lodash';
import * as types from 'js/store/mutations-types';
import { set} from 'vue';

// Initial state
const state = {
	breadcrumbs: [
		/**
		 * {
		 * 	level: Integer,
		 * 	text: String,
		 * 	to: Object,
		 * },
		 * ...
		 */
	],
	lessonState: {
		activeSection: 0,
		activeScreen: 0
	}
};

// Getters
const getters = {
	breadcrumbs: state => _.sortBy(state.breadcrumbs, (crumb) => crumb.level),
	lessonState: state => state.lessonState
};

// Mutations
const mutations = {
	[types.ADD_BREADCRUMB] (state, payload) {
		state.breadcrumbs.push(payload);
	},
	[types.REMOVE_BREADCRUMB] (state, text) {
		let index = _.findIndex(state.breadcrumbs, ['text', text]);

		if (index > -1) {
			state.breadcrumbs.splice(index, 1);
		}
	},
	[types.UPDATE_LESSON_NAV] (state, payload) {
		set(state, 'lessonState', payload);
	}
};

const actions = {
	addBreadcrumb({ commit }, payload) {
		commit(types.ADD_BREADCRUMB, payload);
	},
	removeBreadcrumb({ commit }, text) {
		commit(types.REMOVE_BREADCRUMB, text);
	},
	updateLessonNav({commit}, payload) {
		commit(types.UPDATE_LESSON_NAV, payload);
	}
};

export default {
	state,
	getters,
	mutations,
	actions,
};
