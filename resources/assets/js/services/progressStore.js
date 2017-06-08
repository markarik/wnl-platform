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

	$wnl.logger.debug(`Setting ${key} in localStorage: ${JSON.stringify(updatedCourseProgress)}`);
	store.set(key, updatedCourseProgress);

	getCurrentUser().then(({data: {id}}) => {
		//TODO API call and save progress in DB
		axios.patch(getApiUrl(`users/${id}/state`), {
			lessonId,
			status,
			route,
			courseId
		});
	})
};

const getCourseProgress = ({courseId}) => {
	const key = getCourseStoreKey(courseId);
	const localStorageValue = get(key);

	if (typeof localStorageValue !== 'object') {
		//TODO API call should occur here to make sure there is no progress for user
		//API call result should be saved in local storage in case its present
		return new Promise((resolve) => {
			getCurrentUser()
				.then(({data: {id}}) => {
					return axios.get(getApiUrl(`users/${id}/state`));
				}).then((results) => {
				return resolve({
					lessons: {}
				});
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
	//TODO this should not be exposed
	getLessonStoreKey,
	// TODO this should not be exposed
	remove
};
