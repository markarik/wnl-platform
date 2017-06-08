import store from 'store'
import {getApiUrl} from 'js/utils/env';

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
	//TODO API call and save progress in DB
};

const getCourseProgress = ({courseId}) => {
	const key = getCourseStoreKey(courseId);
	const localStorageValue = get(key);

	if (typeof localStorageValue !== 'object') {
		//TODO API call should occur here to make sure there is no progress for user
		//API call result should be saved in local storage in case its present
		const promisedResult = Promise.resolve({
			lessons: {}
		});

		promisedResult.then(() => {
			// TODO set course progress only when there is something to be set setCourseProgress({courseId})
		});

		return promisedResult;
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
