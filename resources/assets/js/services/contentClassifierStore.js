import engine from 'store/src/store-engine';
import localStorage from 'store/storages/localStorage';

const store = engine.createStore([
	localStorage,
]);

export default store;

export const CONTENT_CLASSIFIER_STORE_KEYS = {
	LAST_TERM: 'content-classifier-last-term',
	ALL_TERMS: 'content-classifier-all-terms'
};
