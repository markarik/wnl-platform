import store from './sessionStore';
import {getCurrentUser} from './user';
import {getApiUrl} from 'js/utils/env';
import _ from 'lodash';

const CACHE_VERSION = 2;

export const getLocalStorageKey = (setId, userSlug) => {
	return `wnl-quiz-${setId}-u-${userSlug}-${CACHE_VERSION}`
};


const saveQuizProgress = (setId, currentUserSlug, state, recordedAnswers = []) => {
	const storeKey = getLocalStorageKey(setId, currentUserSlug);

	if (!state.retry) {
		store.set(storeKey, state);

		getCurrentUser().then(({data: {id}}) => {
			axios.put(getApiUrl(`quiz_results/${id}/quiz/${setId}`), {
				quiz: state,
				recordedAnswers
			});
		});
	}
};

const getQuizProgress = (setId, currentUserSlug) => {
	return new Promise((resolve) => {
			getCurrentUser().then(({data: {id}}) => {
				axios.get(getApiUrl(`quiz_results/${id}/quiz/${setId}`)).then(({data: {quiz}}) => {
					resolve(quiz)
				});
			})
	});
};


export default {
	getQuizProgress,
	saveQuizProgress
}
