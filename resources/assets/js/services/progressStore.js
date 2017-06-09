import store from 'store'
import {getApiUrl} from 'js/utils/env';
import axios from 'axios';
import {getCurrentUser} from './user';

const CACHE_VERSION = 1;

const set = (key, value) => {
	store.set(key, value);
};

const get = (key) => {
	return store.get(key);
};

const setCourseProgress = ({courseId, lessonId, route, status}) => {
	const key = getCourseStoreKey(courseId);
	const courseProgress = get(key) || {};

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
		axios.patch(getApiUrl(`users/${id}/state/course/${courseId}`), updatedCourseProgress);
	})
};

const setLessonProgress = ({courseId, lessonId, route}) => {
	const key = getLessonStoreKey(courseId, lessonId);
	set(key, route);

	getCurrentUser().then(({data: {id}}) => {
		axios.patch(getApiUrl(`users/${id}/state/course/${courseId}/lesson/${lessonId}`), {
			lesson: route
		});
	})
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
	const localStorageValue = get(lessonStoreKey);

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

const remove = (key) => {
	store.remove(key);
	//TODO clear progress table
};

const getLessonStoreKey = (courseId, lessonId) => {
	return `progress-courses-${courseId}-lessons-${lessonId}-${CACHE_VERSION}`
};

const getCourseStoreKey = (courseId) => {
	return `progress-courses-${courseId}-${CACHE_VERSION}`
};


export default {
	// TODO this two should not be exposed
	get,
	set,
	getCourseProgress,
	setCourseProgress,
	getLessonProgress,
	setLessonProgress,
	//TODO this should not be exposed
	getLessonStoreKey,
	// TODO this should not be exposed
	remove
};
