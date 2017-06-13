import _ from 'lodash'
import * as types from '../mutations-types'
import progressStore, {STATUS_COMPLETE, STATUS_IN_PROGRESS} from '../../services/progressStore'
import {set} from 'vue'

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
	getLesson: (state) => (courseId, lessonId) => {
		return state.courses[courseId] && state.courses[courseId].lessons[lessonId];
	},
	getScreen: (state) => (courseId, lessonId, screenId) => {
		return _.get(state.courses[courseId], `lessons[${lessonId}].screens[${screenId}]`);
	},
	wasCourseStarted: (state, getters) => (courseId) => {
		return !_.isEmpty(getters.getCourse(courseId).lessons)
	},
	getSavedLesson: (state, getters) => (courseId, lessonId) => {
		const storeValue = _.get(state.courses[courseId], `lessons[${lessonId}]`);

		if (storeValue) {
			return Promise.resolve(state.courses[courseId].lessons[lessonId]);
		}

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
	shouldCompleteLesson: (state, getters, rootState, rootGetters) => (courseId, lessonId) => {
		const allScreens = rootGetters['course/getScreens'](lessonId);
		const startedScreens = _.get(state.courses[courseId].lessons[lessonId], 'screens');

		if (!startedScreens) {
			return false;
		}

		return !allScreens.find(({id}) => {
			if (!startedScreens[id]) {
				return true;
			} else if (startedScreens[id].status === STATUS_IN_PROGRESS) {
				return true;
			}

			return false;
		});
	},
	shouldCompleteScreen: (state, getters, rootState, rootGetters) => (courseId, lessonId, screenId) => {
		const screen = rootGetters['course/getScreen'](screenId);

		if (!screen.sections) {
			return true;
		}

		const allSections = rootGetters['course/getSections'](screen.sections);
		const lesson = state.courses[courseId].lessons[lessonId];

		if (!_.get(lesson, `screens[${screenId}].sections`)) {
			return false;
		}

		const startedSections = lesson.screens[screenId].sections;

		return !allSections.find(({id}) => {
			return !startedSections[id];
		});
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
	[types.PROGRESS_SETUP_LESSON] (state, payload) {
		set(state.courses[payload.courseId].lessons, payload.lessonId, payload.progressData)
	},
	[types.PROGRESS_START_LESSON] (state, payload) {
		const lessonState = state.courses[payload.courseId].lessons[payload.lessonId];
		//TODO consider issuing one request instead of two when starting lesson
		const updatedState = progressStore.startLesson(lessonState, payload);
		progressStore.setCourseProgress({...payload, status: STATUS_IN_PROGRESS});

		set(state.courses[payload.courseId].lessons, payload.lessonId, updatedState)
	},
	[types.PROGRESS_UPDATE_LESSON] (state, payload) {
		const lessonState = state.courses[payload.courseId].lessons[payload.lessonId];
		const updatedState = progressStore.updateLesson(lessonState, payload);

		set(state.courses[payload.courseId].lessons, payload.lessonId, updatedState);
	},
	[types.PROGRESS_COMPLETE_LESSON] (state, payload) {
		const lessonState = state.courses[payload.courseId].lessons[payload.lessonId];
		// TODO consider issuing one request instead of two when finishing lesson
		const updatedState = progressStore.completeLesson(lessonState, payload);
		progressStore.setCourseProgress({...payload, status: STATUS_COMPLETE});

		set(state.courses[payload.courseId].lessons, payload.lessonId, updatedState)
	},
	[types.PROGRESS_COMPLETE_SECTION] (state, payload) {
		const lessonState = state.courses[payload.courseId].lessons[payload.lessonId];
		const updatedState = progressStore.completeSection(lessonState, payload);

		set(lessonState, 'screens', updatedState.screens);
	},
	[types.PROGRESS_COMPLETE_SCREEN] (state, payload) {
		const lessonState = state.courses[payload.courseId].lessons[payload.lessonId];
		const updatedState = progressStore.completeScreen(lessonState, payload);

		set(lessonState, 'screens', updatedState.screens);
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
		progressStore.getLessonProgress(payload)
			.then(data => {
				commit(types.PROGRESS_SETUP_LESSON, {
					courseId: payload.courseId,
					lessonId: payload.lessonId,
					progressData: data
				});

				if (!getters.wasLessonStarted(payload.courseId, payload.lessonId)) {
					$wnl.logger.info(`Starting lesson ${payload.lessonId}`, payload)
					commit(types.PROGRESS_START_LESSON, payload)
				}
			});
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
	},
	completeScreen({commit}, payload) {
		commit(types.PROGRESS_COMPLETE_SCREEN, payload);
	},
	completeSection({commit}, payload) {
		commit(types.PROGRESS_COMPLETE_SECTION, payload)
	}
}

export default {
	namespaced,
	state,
	getters,
	mutations,
	actions
}
