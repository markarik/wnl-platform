import store from 'store'
import axios from 'axios';
import {getApiUrl} from 'js/utils/env';
import {getCurrentUser} from './user';


// TODO: Mar 9, 2017 - Use config when it's ready
export const STATUS_IN_PROGRESS = 'in-progress';
export const STATUS_COMPLETE = 'complete';

const CACHE_VERSION = 1;

const setCourseProgress = ({courseId, lessonId, route, status}) => {
	const key = getCourseStoreKey(courseId);
	const courseProgress = store.get(key) || {};

	const updatedCourseProgress = {
		...courseProgress,
		lessons: {
			...courseProgress.lessons,
			[lessonId]: {
				status,
				route
			}
		}
	};

	store.set(key, updatedCourseProgress);

	getCurrentUser().then(({data: {id}}) => {
		axios.put(getApiUrl(`users/${id}/state/course/${courseId}`), updatedCourseProgress);
	})
};

const setLessonProgress = ({courseId, lessonId}, value) => {
	const key = getLessonStoreKey(courseId, lessonId);

	store.set(key, value);

	getCurrentUser().then(({data: {id}}) => {
		axios.put(getApiUrl(`users/${id}/state/course/${courseId}/lesson/${lessonId}`), {
			lesson: value
		});
	})
};

const completeSection = (lessonState, {screenId, sectionId, route, ...rest}) => {
	const updatedState = lessonState ? {...lessonState} : {};

	updatedState.route = route;

	updatedState.screens = updatedState.screens || {};
	if (!updatedState.screens[screenId]) {
		updatedState.screens[screenId] = {
			status: STATUS_IN_PROGRESS
		}
	}

	if (!updatedState.screens[screenId].sections) {
		updatedState.screens[screenId].sections = {
			[sectionId]: STATUS_COMPLETE
		}
	} else {
		updatedState.screens[screenId].sections = {
			...updatedState.screens[screenId].sections,
			[sectionId]: STATUS_COMPLETE
		}
	}

	setLessonProgress(rest, updatedState);

	return updatedState;
};

const completeScreen = (lessonState, {screenId, route, ...rest}) => {
	const updatedState = {...lessonState};

	updatedState.route = route;

	updatedState.screens = lessonState.screens || {};
	updatedState.screens[screenId] = updatedState.screens[screenId] || {};
	updatedState.screens[screenId].status = STATUS_COMPLETE;

	setLessonProgress(rest, updatedState);

	return updatedState;
};

const completeLesson = (lessonState, payload) => {
	const updatedState = {
		...lessonState,
		status: STATUS_COMPLETE,
		route: payload.route
	};

	setLessonProgress(payload, updatedState);
	return updatedState;
};

const startLesson = (lessonState, {route, ...payload}) => {
	const updatedState = {
		...lessonState,
		status: STATUS_IN_PROGRESS,
		route
	};

	setLessonProgress(payload, updatedState);
	return updatedState;
};

const resetLessonProgress = ({courseId, lessonId}) => {
	const key = getLessonStoreKey(courseId, lessonId);

	store.remove(key);

	getCurrentUser().then(({data: {id}}) => {
		axios.delete(getApiUrl(`users/${id}/state/course/${courseId}/lesson/${lessonId}`));
	});
};

const getCourseProgress = ({courseId}) => {
	const key = getCourseStoreKey(courseId);
	const localStorageValue = store.get(key);

	if (typeof localStorageValue !== 'object') {
		return new Promise((resolve) => {
			getCurrentUser()
				.then(({data: {id}}) => {
					return axios.get(getApiUrl(`users/${id}/state/course/${courseId}`));
				})
				.then(({data: {lessons} = {}}) => {
					store.set(key, {
						lessons
					});

					return resolve({
						lessons
					})
				});
		});
	} else {
		return Promise.resolve(localStorageValue);
	}
};

const getLessonProgress = ({courseId, lessonId}) => {
	const lessonStoreKey = getLessonStoreKey(courseId, lessonId);
	const localStorageValue = store.get(lessonStoreKey);

	if (typeof localStorageValue !== 'object') {
		return new Promise((resolve) => {
			getCurrentUser()
				.then(({data: {id}}) => {
					return axios.get(getApiUrl(`users/${id}/state/course/${courseId}/lesson/${lessonId}`));
				})
				.then(({data: {lesson}} = {}) => {
					store.set(lessonStoreKey, lesson);

					return resolve(lesson)
				});
		});
	} else {
		return Promise.resolve(localStorageValue);
	}
};

const getLessonStoreKey = (courseId, lessonId) => {
	return `progress-courses-${courseId}-lessons-${lessonId}-${CACHE_VERSION}`
};

const getCourseStoreKey = (courseId) => {
	return `progress-courses-${courseId}-${CACHE_VERSION}`
};


export default {
	getCourseProgress,
	setCourseProgress,
	getLessonProgress,
	resetLessonProgress,
	completeSection,
	completeScreen,
	completeLesson,
	startLesson
};
