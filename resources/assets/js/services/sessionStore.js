import engine from 'store/src/store-engine';
import sessionStorage from 'store/storages/sessionStorage';
import localStorage from 'store/storages/localStorage';
import memoryStorage from 'store/storages/memoryStorage';

const store = engine.createStore([
	sessionStorage,
	localStorage,
	memoryStorage
]);

export default store;

