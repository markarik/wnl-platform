import _ from 'lodash';
import axios from 'axios';
import { set } from 'vue';
import { getApiUrl } from 'js/utils/env';
import * as types from 'js/admin/store/mutations-types';

// Helper functions

// Namespace
const namespaced = true;

// Initial state
const state = {
	ready: false,
	lessons: [],
	// TODO: Fetch groups
	// TODO: Fetch slideshows
	// TODO: Fetch quizes
};

// Getters
const getters = {
	isReady: state => state.ready,
	allLessons: state => {
		return _.sortBy(state.lessons, (lesson) => lesson.order_number);
	},
};

// Mutations
const mutations = {
	[types.LESSONS_READY] (state) {
		set(state, 'ready', true);
	},
	[types.SETUP_LESSONS] (state, payload) {
		set(state, 'lessons', payload);
	},
	[types.ADD_LESSON] (state, lesson) {
		state.lessons.push(lesson);
	}
};

// Actions
const actions = {
	async fetchAllLessons({ commit, getters }) {
		if (_.isEmpty(getters.allLessons)) {
			const response = await axios.get(getApiUrl('lessons/all'));
			commit(types.SETUP_LESSONS, response.data);
		}
	},
	setup({ commit, dispatch }) {
		Promise.all([
			dispatch('fetchAllLessons'),
		]).then(resolutions => {
			$wnl.logger.debug('Lessons editor ready');
			commit(types.LESSONS_READY);
		}, reason => {
			$wnl.logger.error(reason);
		});
	},
	async create({commit}, name) {
		const {data: lesson} = await axios.post(getApiUrl('lessons'), {
			name
		});
		commit(types.ADD_LESSON, lesson);

		return lesson;
	}
};

export default {
	namespaced,
	state,
	getters,
	mutations,
	actions
};
