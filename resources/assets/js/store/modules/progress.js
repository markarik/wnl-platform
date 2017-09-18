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

		return false
	},
	getLesson: (state) => (courseId, lessonId) => {
		return state.courses[courseId] && state.courses[courseId].lessons && state.courses[courseId].lessons[lessonId];
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
			state.courses[courseId].lessons &&
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
	},
	isCourseOver: (state, getters) => (courseId) => {
		return _.size(getters.getCompleteLessons(courseId)) === _.size(state.courses[courseId].lessons)
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
	[types.PROGRESS_START_LESSON] (state, payload) {
		const courseState = state.courses[payload.courseId]
		const updatedLessonState = progressStore.startLesson(courseState, payload);

		set(state.courses[payload.courseId].lessons, payload.lessonId, updatedLessonState)
	},
	[types.PROGRESS_COMPLETE_LESSON] (state, payload) {
		const courseState = state.courses[payload.courseId];
		const updatedLessonState = progressStore.completeLesson(courseState, payload);

		set(state.courses[payload.courseId].lessons, payload.lessonId, updatedLessonState)
	},
	[types.PROGRESS_COMPLETE_SECTION] (state, payload) {
		const lessonState = state.courses[payload.courseId].lessons[payload.lessonId];
		const updatedState = progressStore.completeSection(lessonState, payload);

		set(lessonState, 'screens', updatedState.screens);
		set(lessonState, 'route', payload.route);
	},
	[types.PROGRESS_COMPLETE_SCREEN] (state, payload) {
		const lessonState = state.courses[payload.courseId].lessons[payload.lessonId];
		const updatedState = progressStore.completeScreen(lessonState, payload);

		set(lessonState, 'screens', updatedState.screens);
		set(lessonState, 'route', payload.route);
	},
};

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
		return progressStore.getLessonProgress(payload)
			.then(data => {
				commit(types.PROGRESS_SETUP_LESSON, {
					courseId: payload.courseId,
					lessonId: payload.lessonId,
					progressData: data
				});

				if (!getters.wasLessonStarted(payload.courseId, payload.lessonId)) {
					$wnl.logger.debug(`Starting lesson ${payload.lessonId}`, payload)
					commit(types.PROGRESS_START_LESSON, payload)

					return true;
				}

				return false;
			});
	},
	completeLesson({commit, getters}, payload) {
		if (!getters.isLessonComplete(payload.courseId, payload.lessonId)) {
			$wnl.logger.debug(`Completing lesson ${payload.lessonId}`, payload)
			commit(types.PROGRESS_COMPLETE_LESSON, payload)
		}
	},
	completeScreen({commit}, payload) {
		commit(types.PROGRESS_COMPLETE_SCREEN, payload);
	},
	completeSection({commit}, payload) {
		commit(types.PROGRESS_COMPLETE_SECTION, payload)
	},
};

export default {
	namespaced,
	state,
	getters,
	mutations,
	actions
}
