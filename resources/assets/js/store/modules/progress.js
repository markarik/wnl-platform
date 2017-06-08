import _ from 'lodash'
import * as types from '../mutations-types'
import progressStore from '../../services/progressStore'
import {getApiUrl} from 'js/utils/env'
import {set} from 'vue'

// Statuses
// TODO: Mar 9, 2017 - Use config when it's ready
const STATUS_IN_PROGRESS = 'in-progress'
const STATUS_COMPLETE = 'complete'

function resetLessonProgress(payload) {
	progressStore.remove(progressStore.getLessonStoreKey(payload.courseId, payload.lessonId))
}

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
	},
	wasCourseStarted: (state, getters) => (courseId) => {
		return !_.isEmpty(getters.getCourse(courseId).lessons)
	},
	getSavedLesson: (state) => (courseId, lessonId) => {
		// TODO: Mar 13, 2017 - Check Vuex before asking localStorage
		return progressStore.getLessonProgress({courseId, lessonId});
	},
	wasLessonStarted: (state) => (courseId, lessonId) => {
		return state.courses.hasOwnProperty(courseId) &&
			state.courses[courseId].lessons.hasOwnProperty(lessonId) &&
			state.courses[courseId].lessons[lessonId].hasOwnProperty('status')
	},
	isLessonInProgress: (state, getters) => (courseId, lessonId) => {
		return getters.wasLessonStarted(courseId, lessonId) &&
			state.courses[courseId].lessons[lessonId].status === STATUS_IN_PROGRESS
	},
	getFirstLessonIdInProgress: (state) => (courseId) => {
		let lessons = state.courses[courseId].lessons
		for (var lessonId in lessons) {
			if (lessons[lessonId].status === STATUS_IN_PROGRESS) {
				return lessonId
			}
		}
		return 0
	},
	isLessonComplete: (state, getters) => (courseId, lessonId) => {
		return getters.wasLessonStarted(courseId, lessonId) &&
			state.courses[courseId].lessons[lessonId].status === STATUS_COMPLETE
	},
	getCompleteLessons: (state, getters, rootState, rootGetters) => (courseId) => {
		let lesson, lessons = []
		for (var lessonId in state.courses[courseId].lessons) {
			lesson = rootGetters['course/getLesson'](lessonId)
			if (state.courses[courseId].lessons[lessonId].status === STATUS_COMPLETE) {
				lessons.push(lesson)
			}
		}
		return lessons
	}
}

// Mutations
const mutations = {
	[types.PROGRESS_SETUP_COURSE] (state, payload) {
		set(state.courses, payload.courseId, payload.progressData)
	},
	[types.PROGRESS_START_LESSON] (state, payload) {
		progressStore.setCourseProgress({...payload, status: STATUS_IN_PROGRESS});

		set(state.courses[payload.courseId].lessons, payload.lessonId, {
			status: STATUS_IN_PROGRESS,
			route: payload.route,
		})
	},
	[types.PROGRESS_UPDATE_LESSON] (state, payload) {
		console.log("PROGRESS_UPDATE_LESSON", payload);
		progressStore.setLessonProgress(payload);
		set(state.courses[payload.courseId].lessons[payload.lessonId], 'route', payload.route)
	},
	[types.PROGRESS_COMPLETE_LESSON] (state, payload) {
		console.log("PROGRESS_COMPLETE_LESSON", payload);
		progressStore.setCourseProgress({...payload, status: STATUS_COMPLETE});
		set(state.courses[payload.courseId].lessons[payload.lessonId], 'status', STATUS_COMPLETE)
		resetLessonProgress(payload)
	}
}

// Actions
const actions = {
	setupCourse({commit}, courseId) {
		return new Promise((resolve) => {
			progressStore.getCourseProgress({courseId})
				.then(data => {
					commit(types.PROGRESS_SETUP_COURSE, {
						courseId: courseId,
						progressData: data
					})
					resolve()
				})
				.catch(exception => $wnl.logger.capture(exception))
		})
	},
	startLesson({commit, getters}, payload) {
		if (!getters.wasLessonStarted(payload.courseId, payload.lessonId)) {
			$wnl.logger.info(`Starting lesson ${payload.lessonId}`, payload)
			commit(types.PROGRESS_START_LESSON, payload)
		}
	},
	updateLesson({commit}, payload) {
		$wnl.logger.debug(`Updating lesson ${payload.lessonId}`)
		commit(types.PROGRESS_UPDATE_LESSON, payload)
	},
	completeLesson({commit, getters}, payload) {
		if (!getters.isLessonComplete(payload.courseId, payload.lessonId)) {
			$wnl.logger.info(`Completing lesson ${payload.lessonId}`, payload)
			commit(types.PROGRESS_COMPLETE_LESSON, payload)
		}
	}
}

export default {
	namespaced,
	state,
	getters,
	mutations,
	actions
}
