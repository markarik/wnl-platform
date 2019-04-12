import axios from 'axios';
import _ from 'lodash';
import {set} from 'vue';

import * as types from 'js/store/mutations-types';
import progressStore, {STATUS_COMPLETE, STATUS_IN_PROGRESS} from 'js/services/progressStore';
import { getApiUrl } from 'js/utils/env';
import {USER_SETTING_NAMES} from 'js/consts/settings';

// Namespace
const namespaced = true;

// Initial state
const state = {
	courses: {},
};

// Getters
const getters = {
	getCourse: (state) => (courseId) => {
		if (state.courses.hasOwnProperty(courseId)) {
			return state.courses[courseId];
		}

		return false;
	},
	getLesson: (state) => (courseId, lessonId) => {
		return _.get(state.courses[courseId], `lessons[${lessonId}]`, {}) || {};
	},
	getScreen: (state) => (courseId, lessonId, screenId) => {
		return _.get(state.courses[courseId], `lessons[${lessonId}].screens[${screenId}]`);
	},
	getSection: (state) => (courseId, lessonId, screenId, sectionId) => {
		return _.get(state.courses[courseId], `lessons[${lessonId}].screens[${screenId}].sections[${sectionId}]`);
	},
	getSavedLesson: (state) => (courseId, lessonId, profileId) => {
		const storeValue = _.get(state.courses[courseId], `lessons[${lessonId}]`);

		if (storeValue) {
			return Promise.resolve(state.courses[courseId].lessons[lessonId]);
		}

		return progressStore.getLessonProgress({courseId, lessonId, profileId});
	},
	wasLessonStarted: (state, getters) => (courseId, lessonId) => {
		const lessonProgress = getters.getLesson(courseId, lessonId);
		return lessonProgress.hasOwnProperty('status');
	},
	isLessonInProgress: (state, getters) => (courseId, lessonId) => {
		return getters.wasLessonStarted(courseId, lessonId) &&
			state.courses[courseId].lessons[lessonId].status === STATUS_IN_PROGRESS;
	},
	getFirstLessonInProgress: (state, getters, rootState, rootGetters) => (courseId) => {
		let lessons = rootGetters['course/getRequiredLessons'];

		return lessons.find(({id: lessonId, isAvailable}) => {
			const lessonProgress = state.courses[courseId].lessons[lessonId];
			return lessonProgress && lessonProgress.status === STATUS_IN_PROGRESS && isAvailable === true;
		});
	},
	isLessonComplete: (state, getters) => (courseId, lessonId) => {
		return getters.wasLessonStarted(courseId, lessonId) &&
			state.courses[courseId].lessons[lessonId].status === STATUS_COMPLETE;
	},
	getCompleteLessons: (state, getters, rootState, rootGetters) => (courseId) => {
		let lesson, lessons = [];
		for (var lessonId in state.courses[courseId].lessons) {
			lesson = rootGetters['course/getLesson'](lessonId);
			if (state.courses[courseId].lessons[lessonId].status === STATUS_COMPLETE && lesson.is_required) {
				lessons.push(lesson);
			}
		}
		return lessons;
	},
};

// Mutations
const mutations = {
	[types.PROGRESS_SETUP_COURSE] (state, payload) {
		set(state.courses, payload.courseId, payload.progressData);
	},
	[types.PROGRESS_SETUP_LESSON] (state, payload) {
		const updatedState = {
			...((state.courses[payload.courseId] &&  state.courses[payload.courseId].lessons)|| []),
			[payload.lessonId]: payload.progressData
		};
		set(state.courses[payload.courseId], 'lessons', updatedState);
	},
	[types.PROGRESS_START_LESSON] (state, { payload, updatedLessonState }) {
		set(state.courses[payload.courseId].lessons, payload.lessonId, updatedLessonState);
	},
	[types.PROGRESS_COMPLETE_LESSON] (state, { payload, updatedLessonState }) {
		set(state.courses[payload.courseId].lessons, payload.lessonId, updatedLessonState);
	},
	[types.PROGRESS_COMPLETE_SECTION] (state, { updatedState, payload }) {
		const lessonState = state.courses[payload.courseId].lessons[payload.lessonId];

		set(lessonState, 'screens', updatedState.screens);
		set(lessonState, 'route', payload.route);
	},
	[types.PROGRESS_COMPLETE_SUBSECTION] (state, { updatedState, payload }) {
		const lessonState = state.courses[payload.courseId].lessons[payload.lessonId];

		set(lessonState, 'screens', updatedState.screens);
		set(lessonState, 'route', payload.route);
	},
	[types.PROGRESS_COMPLETE_SCREEN] (state, { updatedState, payload }) {
		const lessonState = state.courses[payload.courseId].lessons[payload.lessonId];

		set(lessonState, 'screens', updatedState.screens);
		set(lessonState, 'route', payload.route);
	},
};

// Actions
const actions = {
	async setupCourse({commit, rootGetters, dispatch}, courseId = 1) {
		await dispatch('setupCurrentUser', {}, { root: true });
		try {
			const data = await progressStore.getCourseProgress({
				courseId,
				profileId: rootGetters.currentUserProfileId,
			});
			commit(types.PROGRESS_SETUP_COURSE, {
				courseId: courseId,
				progressData: data,
			});
		} catch (error) {
			$wnl.logger.capture(error);
		}
	},
	async startLesson({commit, getters, dispatch, rootGetters}, payload) {
		await dispatch('setupCurrentUser', {}, { root: true });
		const data = await progressStore.getLessonProgress({
			...payload,
			profileId: rootGetters.currentUserProfileId,
		});
		commit(types.PROGRESS_SETUP_LESSON, {
			courseId: payload.courseId,
			lessonId: payload.lessonId,
			progressData: data
		});

		if (!getters.wasLessonStarted(payload.courseId, payload.lessonId)) {
			$wnl.logger.debug(`Starting lesson ${payload.lessonId}`, payload);

			await dispatch('setupCurrentUser', {}, {root: true});

			const courseState = state.courses[payload.courseId];
			const updatedLessonState = progressStore.startLesson(courseState, {
				...payload,
				profileId: rootGetters.currentUserProfileId,
			});
			commit(types.PROGRESS_START_LESSON, { payload, updatedLessonState });

			return true;
		}

		return false;
	},
	async completeLesson({commit, getters, rootGetters, dispatch}, payload) {
		if (!getters.isLessonComplete(payload.courseId, payload.lessonId)) {
			$wnl.logger.debug(`Completing lesson ${payload.lessonId}`, payload);

			await dispatch('setupCurrentUser', {}, {root: true});

			const courseState = state.courses[payload.courseId];
			const updatedLessonState = progressStore.completeLesson(courseState, {
				...payload,
				profileId: rootGetters.currentUserProfileId,
			});

			commit(types.PROGRESS_COMPLETE_LESSON, {payload, updatedLessonState});
		}
	},
	async completeScreen({commit, rootGetters, dispatch, getters}, payload) {
		await dispatch('setupCurrentUser', {}, {root: true});

		const lessonState = _.cloneDeep(getters.getLesson(payload.courseId, payload.lessonId));
		const updatedState = progressStore.completeScreen(lessonState, {
			...payload,
			profileId: rootGetters.currentUserProfileId,
		});

		commit(types.PROGRESS_COMPLETE_SCREEN, {updatedState, payload});
	},
	async completeSection({commit, rootGetters, dispatch, getters}, payload) {
		await dispatch('setupCurrentUser', {}, {root: true});

		const lessonState = _.cloneDeep(getters.getLesson(payload.courseId, payload.lessonId));

		const updatedState = progressStore.completeSection(lessonState, {
			...payload,
			profileId: rootGetters.currentUserProfileId,
		});

		commit(types.PROGRESS_COMPLETE_SECTION, {updatedState, payload});
	},
	async completeSubsection({commit, rootGetters, dispatch, getters}, payload) {
		await dispatch('setupCurrentUser', {}, {root: true});

		const lessonState = _.cloneDeep(getters.getLesson(payload.courseId, payload.lessonId));
		const updatedState = progressStore.completeSubsection(lessonState, {
			...payload,
			profileId: rootGetters.currentUserProfileId,
		});
		commit(types.PROGRESS_COMPLETE_SUBSECTION, {updatedState, payload});
	},
	async deleteProgress({dispatch, rootGetters}) {
		const userId = rootGetters.currentUserId;
		await axios.delete(getApiUrl(`users/${userId}/state/course/1`));
		dispatch('updateCurrentUser', {
			hasFinishedEntryExam: false
		}, {
			root: true
		});
		dispatch('changeUserSettingAndSync', {
			setting: USER_SETTING_NAMES.SKIP_SATISFACTION_GUARANTEE_MODAL,
			value: false,
		}, {
			root: true
		});
	}
};

export default {
	namespaced,
	state,
	getters,
	mutations,
	actions
};
