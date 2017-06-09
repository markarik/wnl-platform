import store from 'store'
import axios from 'axios';
import {getApiUrl} from 'js/utils/env';
import {getCurrentUser} from './user';

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

const setLessonProgress = ({courseId, lessonId, route}) => {
	const key = getLessonStoreKey(courseId, lessonId);

	store.set(key, route);

	getCurrentUser().then(({data: {id}}) => {
		axios.put(getApiUrl(`users/${id}/state/course/${courseId}/lesson/${lessonId}`), {
			lesson: route
		});
	})
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
				.then(({data = {}}) => {
					store.set(key, {
						lessons: data
					});

					return resolve({
						lessons: data
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
				.then(({data = {}}) => {
					store.set(lessonStoreKey, data);

					return resolve(data)
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
	setLessonProgress,
	resetLessonProgress
};
