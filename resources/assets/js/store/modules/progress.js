import _ from 'lodash'
import * as types from '../mutations-types'
import progressStore, {STATUS_COMPLETE, STATUS_IN_PROGRESS} from 'js/services/progressStore'
import {set} from 'vue'
import { getApiUrl } from 'js/utils/env';

// Namespace
const namespaced = true

// Initial state
const state = {
	courses: {},
}

// Getters
const getters = {
	getCourse: (state) => (courseId) => {
		if (state.courses.hasOwnProperty(courseId)) {
			return state.courses[courseId]
		}

		return false
	},
	getLesson: (state) => (courseId, lessonId) => {
		return _.get(state.courses[courseId], `lessons.${lessonId}`, {}) || {}
	},
	getScreen: (state) => (courseId, lessonId, screenId) => {
		return _.get(state.courses[courseId], `lessons[${lessonId}].screens[${screenId}]`);
	},
	getSection: (state) => (courseId, lessonId, screenId, sectionId) => {
		return _.get(state.courses[courseId], `lessons[${lessonId}].screens[${screenId}].sections[${sectionId}]`);
	},
	wasCourseStarted: (state, getters) => (courseId) => {
		return !_.isEmpty(getters.getCourse(courseId).lessons)
	},
	getSavedLesson: (state, getters) => (courseId, lessonId, profileId) => {
		const storeValue = _.get(state.courses[courseId], `lessons[${lessonId}]`);

		if (storeValue) {
			return Promise.resolve(state.courses[courseId].lessons[lessonId]);
		}

		return progressStore.getLessonProgress({courseId, lessonId, profileId});
	},
	wasLessonStarted: (state, getters) => (courseId, lessonId) => {
		const lessonProgress = getters.getLesson(courseId, lessonId);
		return lessonProgress.hasOwnProperty('status')
	},
	isLessonInProgress: (state, getters) => (courseId, lessonId) => {
		return getters.wasLessonStarted(courseId, lessonId) &&
			state.courses[courseId].lessons[lessonId].status === STATUS_IN_PROGRESS
	},
	getFirstLessonIdInProgress: (state, getters, rootState, rootGetters) => (courseId) => {
		let lessons = state.courses[courseId].lessons

		return Object.keys(lessons).find((lessonId) => {
			const lesson = rootGetters['course/getLesson'](lessonId)
			return lessons[lessonId].status === STATUS_IN_PROGRESS && lesson.isAvailable === true
		}) || 0
	},
	isLessonComplete: (state, getters) => (courseId, lessonId) => {
		return getters.wasLessonStarted(courseId, lessonId) &&
			state.courses[courseId].lessons[lessonId].status === STATUS_COMPLETE
	},
	getCompleteLessons: (state, getters, rootState, rootGetters) => (courseId) => {
		let lesson, lessons = []
		for (var lessonId in state.courses[courseId].lessons) {
			lesson = rootGetters['course/getLesson'](lessonId)
			if (state.courses[courseId].lessons[lessonId].status === STATUS_COMPLETE && lesson.is_required) {
				lessons.push(lesson)
			}
		}
		return lessons
	},
}

// Mutations
const mutations = {
	[types.PROGRESS_SETUP_COURSE] (state, payload) {
		set(state.courses, payload.courseId, payload.progressData)
	},
	[types.PROGRESS_SETUP_LESSON] (state, payload) {
		const updatedState = {
			...((state.courses[payload.courseId] &&  state.courses[payload.courseId].lessons)|| []),
			[payload.lessonId]: payload.progressData
		};
		set(state.courses[payload.courseId], 'lessons', updatedState)
	},
	[types.PROGRESS_START_LESSON] (state, { payload, updatedLessonState }) {
		set(state.courses[payload.courseId].lessons, payload.lessonId, updatedLessonState)
	},
	[types.PROGRESS_COMPLETE_LESSON] (state, { payload, updatedLessonState }) {
		set(state.courses[payload.courseId].lessons, payload.lessonId, updatedLessonState)
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
			$wnl.logger.debug(`Starting lesson ${payload.lessonId}`, payload)

			await dispatch('setupCurrentUser', {}, {root: true});

			const courseState = state.courses[payload.courseId]
			const updatedLessonState = progressStore.startLesson(courseState, {
				...payload,
				profileId: rootGetters.currentUserProfileId,
			});
			commit(types.PROGRESS_START_LESSON, { payload, updatedLessonState })

			return true;
		}

		return false;
	},
	async completeLesson({commit, getters, rootGetters, dispatch}, payload) {
		if (!getters.isLessonComplete(payload.courseId, payload.lessonId)) {
			$wnl.logger.debug(`Completing lesson ${payload.lessonId}`, payload)

			await dispatch('setupCurrentUser', {}, {root: true});

			const courseState = state.courses[payload.courseId];
			const updatedLessonState = progressStore.completeLesson(courseState, {
				...payload,
				profileId: rootGetters.currentUserProfileId,
			});

			commit(types.PROGRESS_COMPLETE_LESSON, {payload, updatedLessonState})
		}
	},
	async completeScreen({commit, rootGetters, dispatch}, payload) {
		await dispatch('setupCurrentUser', {}, {root: true});

		const lessonState = state.courses[payload.courseId].lessons[payload.lessonId];
		const updatedState = progressStore.completeScreen(lessonState, {
			...payload,
			profileId: rootGetters.currentUserProfileId,
		});

		commit(types.PROGRESS_COMPLETE_SCREEN, {updatedState, payload});
	},
	async completeSection({commit, rootGetters, dispatch}, payload) {
		await dispatch('setupCurrentUser', {}, {root: true});

		const lessonState = state.courses[payload.courseId].lessons[payload.lessonId];

		const updatedState = progressStore.completeSection(lessonState, {
			...payload,
			profileId: rootGetters.currentUserProfileId,
		});

		commit(types.PROGRESS_COMPLETE_SECTION, {updatedState, payload})
	},
	async completeSubsection({commit, rootGetters, dispatch}, payload) {
		await dispatch('setupCurrentUser', {}, {root: true});

		const lessonState = state.courses[payload.courseId].lessons[payload.lessonId];

		const updatedState = progressStore.completeSubsection(lessonState, {
			...payload,
			profileId: rootGetters.currentUserProfileId,
		});
		commit(types.PROGRESS_COMPLETE_SUBSECTION, {updatedState, payload})
	},
	deleteProgress({rootGetters}, payload) {
		const userId = rootGetters.currentUserId
		return axios.delete(getApiUrl(`users/${userId}/state/course/1`));
	}
};

export default {
	namespaced,
	state,
	getters,
	mutations,
	actions
}
