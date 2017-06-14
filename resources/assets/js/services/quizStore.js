import store from 'store';
import {getCurrentUser} from './user';
import {getApiUrl} from 'js/utils/env';
import _ from 'lodash';

const CACHE_VERSION = 2;

export const getLocalStorageKey = (setId, userSlug) => {
	return `wnl-quiz-${setId}-u-${userSlug}-${CACHE_VERSION}`
};


const saveQuizProgress = (setId, currentUserSlug, state) => {
	// TODO: Apr 24, 2017 - We must solve it better.
	const storeKey = getLocalStorageKey(setId, currentUserSlug);
	store.set(storeKey, state, new Date().getTime() + 3 * 60 * 60 * 1000);

	getCurrentUser().then(({data: {id}}) => {
		axios.put(getApiUrl(`users/${id}/state/quiz/${setId}`), {
			quiz: state
		});
	});
};

const getQuizProgress = (setId, currentUserSlug) => {
	let storeKey = getLocalStorageKey(setId, currentUserSlug),
		storedState = store.get(storeKey);

	return new Promise((resolve) => {
		if (_.isEmpty(storedState)) {
			getCurrentUser().then(({data: {id}}) => {
				axios.get(getApiUrl(`users/${id}/state/quiz/${setId}`)).then(({data: {quiz}}) => {
					resolve(quiz)
				});
			})
		} else {
			resolve(storedState);
		}
	});
};


export default {
	getQuizProgress,
	saveQuizProgress
}
